/* 
  MEDIMITRA AUTH HANDLER
  Handles Login, Session, and Role-based redirection
*/

const auth = {
    async login(email, password, persist = true) {
        try {
            const response = await api.post('/auth/login', { email, password });
            
            if (response.success) {
                const storage = persist ? localStorage : sessionStorage;
                storage.setItem('token', response.token);
                storage.setItem('user', JSON.stringify(response.user));
                return { success: true, user: response.user };
            }
            return { success: false, message: response.message || 'Login failed' };
        } catch (error) {
            console.error("Auth Protocol Failure:", error.message);
            // Surface the actual error message if it's a known API error
            const errorMsg = error.message.includes('Unexpected token') 
                ? 'Server synchronization failed (Invalid Response).' 
                : (error.message || 'System connection failed.');
            return { success: false, message: errorMsg };
        }
    },

    async logout() {
        // Revoke token on backend first (blacklists the token server-side)
        try {
            await api.post('/auth/logout', {});
        } catch (e) {
            // Backend unavailable — still clear local session
            console.warn('Backend logout failed, clearing local session anyway.');
        }

        localStorage.removeItem('token');
        localStorage.removeItem('user');
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('user');

        // Determine path to return to index.html safely
        const relativePath = window.location.pathname.endsWith('index.html') ? '' : '../';
        window.location.href = relativePath + 'index.html';
    },

    getUser() {
        const user = localStorage.getItem('user') || sessionStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },

    updateUser(user) {
        const userStr = JSON.stringify(user);
        if (localStorage.getItem('user')) localStorage.setItem('user', userStr);
        if (sessionStorage.getItem('user')) sessionStorage.setItem('user', userStr);
    },

    isLoggedIn() {
        return !!(localStorage.getItem('token') || sessionStorage.getItem('token'));
    },

    redirectByRole(role) {
        if (!role) return;
        const relativePath = window.location.pathname.endsWith('index.html') ? './' : '../';
        const routes = {
            'admin': `${relativePath}admin/admin-dashboard.html`,
            'user': `${relativePath}user/user-dashboard.html`,
            'vendor': `${relativePath}vendor/vendor-dashboard.html`,
            'delivery': `${relativePath}delivery/delivery-dashboard.html`
        };
        window.location.href = routes[role] || `${relativePath}index.html`;
    },

    // Secure Session Guard
    checkSession() {
        const path = window.location.pathname;
        const isPublic = path.endsWith('index.html') || path.includes('auth/') || path === '/' || path === '';
        const isClinicalArea = path.includes('/admin/') || path.includes('/user/') || path.includes('/vendor/') || path.includes('/delivery/');
        const isLoggedIn = this.isLoggedIn();

        // Auto-redirect logged in users away from login/register
        if ((path.includes('login.html') || path.includes('register.html')) && isLoggedIn) {
            const user = this.getUser();
            if (user) return this.redirectByRole(user.role);
        }
        if (isClinicalArea) {
            if (!this.isLoggedIn()) {
                console.error("Secure Session Missing. Redirecting to Healthcare Login node.");
                const rel = path.includes('/admin/') || path.includes('/user/') || path.includes('/vendor/') || path.includes('/delivery/') ? '../' : '';
                const loginPage = path.includes('/admin/') ? 'auth/admin-login.html' : 'auth/login.html';
                window.location.href = rel + loginPage;
                return;
            }

            const user = this.getUser();
            const currentRole = user?.role;
            
            // Role Guard: Ensure user is in their designated area
            let isAuthorized = 
                (path.includes('/admin/') && currentRole === 'admin') ||
                (path.includes('/user/') && currentRole === 'user') ||
                (path.includes('/vendor/') && currentRole === 'vendor') ||
                (path.includes('/delivery/') && currentRole === 'delivery');

            // Whitelist shared pages like order-details.html for authorized roles
            if (path.includes('order-details.html') && (currentRole === 'vendor' || currentRole === 'admin' || currentRole === 'user')) {
                isAuthorized = true;
            }

            if (!isAuthorized) {
                console.warn("Unauthorized Access Attempt. Redirecting to appropriate node.");
                this.redirectByRole(currentRole);
            }
        }
    }
};

// Auto-run session guard
auth.checkSession();

// Handle Login Form Submission
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const persist = e.target.querySelector('input[type="checkbox"]')?.checked;
    const submitBtn = e.target.querySelector('button');

    // Rate limiting check
    if (typeof loginAttempts !== 'undefined' && typeof lockoutUntil !== 'undefined') {
        if (Date.now() < lockoutUntil) {
            const remaining = Math.ceil((lockoutUntil - Date.now()) / 1000);
            const msg = `Too many attempts. Try again in ${remaining}s.`;
            if (window.components && window.components.showError) {
                window.components.showError(msg);
            } else { alert(msg); }
            return;
        }
    }

    // Client-side validation
    let hasError = false;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email) {
        if (typeof showFieldError === 'function') showFieldError('email', 'email-error', 'Email is required.');
        hasError = true;
    } else if (!emailRegex.test(email)) {
        if (typeof showFieldError === 'function') showFieldError('email', 'email-error', 'Enter a valid email address.');
        hasError = true;
    }

    if (!password) {
        if (typeof showFieldError === 'function') showFieldError('password', 'password-error', 'Password is required.');
        hasError = true;
    } else if (password.length < 6) {
        if (typeof showFieldError === 'function') showFieldError('password', 'password-error', 'Password must be at least 6 characters.');
        hasError = true;
    }

    if (hasError) return;

    submitBtn.disabled = true;
    submitBtn.innerText = 'Signing in...';

    const result = await auth.login(email, password, persist);

    if (result.success) {
        auth.redirectByRole(result.user.role);
    } else {
        // Track failed attempts for rate limiting
        if (typeof loginAttempts !== 'undefined') {
            loginAttempts++;
            if (loginAttempts >= 5) {
                lockoutUntil = Date.now() + 30000; // 30 second lockout
                loginAttempts = 0;
            }
        }

        if (window.components && window.components.showError) {
            window.components.showError(result.message);
        } else {
            alert(result.message);
        }
        submitBtn.disabled = false;
        submitBtn.innerHTML = `Sign In <i data-lucide="log-in"></i>`;
        lucide.createIcons();
    }
});
