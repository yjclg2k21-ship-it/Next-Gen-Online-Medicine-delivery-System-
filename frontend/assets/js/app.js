/* 
  MEDIMITRA APP UI HANDLER
  Manages UI interactions, modals, and global state
*/

document.addEventListener('DOMContentLoaded', () => {
    // 1. Loader Logic
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 500);
        }, 800);
    }

    // 2. Modal Open/Close Logic (Liquid show/hide transitions)
    const loginModal = document.getElementById('loginModal');
    const openTriggers = document.querySelectorAll('.open-login-trigger');
    const closeBtn = document.getElementById('closeLoginModal');

    const openModal = (e) => {
        if (e) e.preventDefault();
        loginModal?.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Auto-focus email field
        const emailInput = document.getElementById('email');
        if (emailInput) setTimeout(() => emailInput.focus(), 150);
    };

    const closeModal = () => {
        loginModal?.classList.remove('show');
        document.body.style.overflow = 'auto';
    };

    openTriggers.forEach(btn => btn.addEventListener('click', openModal));
    closeBtn?.addEventListener('click', closeModal);

    // Close on outside click of the card
    window.addEventListener('click', (e) => {
        if (e.target === loginModal) closeModal();
    });

    // Close on Escape key press
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && loginModal?.classList.contains('show')) {
            closeModal();
        }
    });

    // 3. Password Visibility Eye Toggle
    const toggleBtn = document.getElementById('togglePass');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const passInput = document.getElementById('password');
            const isPass = passInput.getAttribute('type') === 'password';
            passInput.setAttribute('type', isPass ? 'text' : 'password');
            this.innerHTML = `<i data-lucide="${isPass ? 'eye-off' : 'eye'}" style="width: 20px; height: 20px;"></i>`;
            lucide.createIcons();
        });
    }

    // 4. Custom Login Form Submission Hook (Stops default auth.js bubble)
    const loginForm = document.getElementById('loginForm');
    if (loginForm && (window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('/') || window.location.pathname === '')) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation(); // Block global auth.js listener

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = loginForm.querySelector('button[type="submit"]');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span style="display:inline-flex; align-items:center; gap:8px;"><span class="spinner" style="border: 2px solid rgba(255,255,255,0.3); border-top: 2px solid white; border-radius: 50%; width: 16px; height: 16px; animation: spin 0.8s linear infinite;"></span> Authenticating...</span>';
            }

            // Run login API call
            const result = await auth.login(email, password, true);

            if (result.success) {
                if (window.components && window.components.showSuccess) {
                    window.components.showSuccess('Access granted. Synchronizing session...');
                }
                setTimeout(() => {
                    auth.redirectByRole(result.user.role);
                }, 1000);
            } else {
                if (window.components && window.components.showError) {
                    window.components.showError(result.message);
                } else {
                    alert(result.message);
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Sign In';
                }
            }
        });
    }

    // 5. Navigation Highlighting
    const links = document.querySelectorAll('.nav-links a');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            links.forEach(l => l.classList.remove('active'));
            e.target.classList.add('active');
        });
    });

    // 6. Global Search Handler
    const searchBtn = document.querySelector('.eco-search-btn');
    const searchInput = document.querySelector('.eco-search-container input');
    
    if (searchBtn && searchInput) {
        const handleSearch = () => {
            const query = searchInput.value.trim();
            const rel = window.location.pathname.includes('/user/') ? '' : 'user/';
            if (query) {
                window.location.href = `${rel}medicines-page.html?q=${encodeURIComponent(query)}`;
            }
        };

        searchBtn.addEventListener('click', handleSearch);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleSearch();
        });
    }
});

// Utility for health charts animation
const animateCharts = () => {
    const bars = document.querySelectorAll('.bar');
    bars.forEach(bar => {
        const height = bar.style.height;
        bar.style.height = '0';
        setTimeout(() => {
            bar.style.height = height;
        }, 100);
    });
};

animateCharts();
