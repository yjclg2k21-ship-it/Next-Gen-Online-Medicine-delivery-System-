/* 
  MEDIMITRA UI LOADER
  Automatically injects layouts based on page context
*/

document.addEventListener('DOMContentLoaded', () => {
    // 5. Hide Loader Guard - ALWAYS hide loader eventually even if scripts fail
    const loader = document.getElementById('loader');
    const hideLoader = () => {
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 300);
        }
    };
    
    // Set a safety timeout
    const safetyTimeout = setTimeout(hideLoader, 2000);

    try {
        const user = (window.auth && typeof auth.getUser === 'function') ? auth.getUser() : null;
        const path = window.location.pathname;
        
        // Determine Role based on path
        let role = null;
        if (path.includes('/admin/')) role = 'admin';
        else if (path.includes('/vendor/')) role = 'vendor';
        else if (path.includes('/user/')) role = 'user';
        else if (path.includes('/delivery/')) role = 'delivery';

        // 1. Inject Header if header-root exists
        const headerRoot = document.getElementById('header-root');
        if (headerRoot && window.components) {
            const isInternalDashboard = role === 'admin' || role === 'vendor' || role === 'delivery';
            headerRoot.innerHTML = components.header(user, isInternalDashboard);
        }

        // 2. Inject Sidebar if sidebar-root exists
        const sidebarRoot = document.getElementById('sidebar-root');
        if (sidebarRoot && role && window.components) {
            sidebarRoot.innerHTML = components.sidebar(role);
        }

        // 3. Inject Footer if footer-root exists
        const footerRoot = document.getElementById('footer-root');
        if (footerRoot && window.components) {
            footerRoot.innerHTML = components.footer();
        }

        // 4. Global Auth Protect
        const isAuthPage = path.includes('/auth/') || path.includes('index.html') || path === '/' || path.includes('/public/') || path.includes('/guest/');
        if (!isAuthPage && window.auth && !auth.isLoggedIn()) {
            const rel = path.endsWith('index.html') || path.endsWith('/') ? './' : '../';
            window.location.href = rel + 'auth/login.html';
        }

        // Success - hide loader faster
        clearTimeout(safetyTimeout);
        hideLoader();
    } catch (e) {
        console.error("UI Loader Error:", e);
        hideLoader();
    }
});
