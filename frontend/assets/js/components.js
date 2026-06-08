/* 
  MEDIMITRA COMPONENT TEMPLATES
  Centralized UI elements for the clinical ecosystem
*/

const components = {
    // Global Feedback Utilities
    showError(msg) {
        this.showNotification(msg, 'danger');
    },
    showSuccess(msg) {
        this.showNotification(msg, 'success');
    },
    toggleSidebar() {
        const sidebar = document.querySelector('.liquid-sidebar');
        if (!sidebar) return;
        sidebar.classList.toggle('open');
        
        let overlay = document.getElementById('sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'sidebar-overlay';
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:10001;opacity:0;transition:opacity 0.3s;pointer-events:none;';
            overlay.onclick = () => components.toggleSidebar();
            
            const squircle = document.querySelector('.squircle-card');
            if (squircle) {
                squircle.appendChild(overlay);
            } else {
                document.body.appendChild(overlay);
            }
        }
        
        if (sidebar.classList.contains('open')) {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
        } else {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
        }
    },
    enableDragScroll(slider) {
        if (!slider) return;
        let isDown = false;
        let startX;
        let scrollLeft;
        let moved = false;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            moved = false;
            slider.style.cursor = 'grabbing';
            startX = e.clientX;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.style.cursor = 'grab';
        });
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.style.cursor = 'grab';
        });
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.clientX;
            const walk = (x - startX) * 1.5;
            if (Math.abs(walk) > 5) {
                moved = true;
            }
            e.preventDefault();
            slider.scrollLeft = scrollLeft - walk;
        });
        slider.addEventListener('click', (e) => {
            if (moved) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    },
    showLoader: () => {
        const loader = document.createElement('div');
        loader.id = 'l-global-loader';
        loader.style = 'position:fixed; top:20px; right:20px; background:white; padding:12px 20px; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,0.1); display:flex; align-items:center; gap:10px; z-index:9999; font-weight:700; border:1px solid rgba(0,122,255,0.2);';
        loader.innerHTML = `<div class="animate-spin" style="width:18px;height:18px;border:3px solid #f3f3f3;border-top:3px solid #0D9488;border-radius:50%;"></div> Syncing Node...`;
        document.body.appendChild(loader);
    },
    // New method to render user top navigation bar
    renderUserTopBar: () => {
        if (typeof window !== 'undefined' && typeof document !== 'undefined') {
            setTimeout(() => {
                if (!document.getElementById('user-top-nav')) {
                    const style = document.createElement('style');
                    style.innerHTML = `
                        #user-top-nav {
                            position: fixed; top: 0; left: 0; width: 100%; height: 68px;
                            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
                            border-bottom: 1px solid rgba(0,0,0,0.06); z-index: 10000;
                            display: flex; align-items: center; justify-content: space-between; padding: 0 32px;
                            box-shadow: 0 4px 24px rgba(0,0,0,0.02); box-sizing: border-box;
                        }
                        .user-top-brand { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1.3rem; letter-spacing: -0.04em; color: #1d1d1f; text-decoration: none; }
                        .user-top-brand span { color: #0D9488; }
                        .user-top-tabs { display: flex; gap: 8px; }
                        .user-top-tab {
                            padding: 10px 18px; border-radius: 12px; color: #6e6e73; font-weight: 700; font-size: 0.9rem;
                            text-decoration: none; transition: all 0.2s ease;
                        }
                        .user-top-tab:hover { background: rgba(0,0,0,0.04); color: #1d1d1f; }
                        .user-top-tab.active { background: #1d1d1f; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                    `;
                    document.head.appendChild(style);
                    const userObj = auth.getUser();
                    const userName = userObj ? userObj.name : 'Patient';
                    const userInitials = userName.split(' ').map(n => n[0]).join('').slice(0,2).toUpperCase();
                    const topNav = document.createElement('div');
                    topNav.id = 'user-top-nav';
                    topNav.innerHTML = `
                        <a href="${homeLink}" class="user-top-brand">➕ Medi<span>Mitra</span> <span style="font-size:0.7rem; background:#0D9488; color:white; padding:2px 6px; border-radius:6px; margin-left:8px;">PATIENT</span></a>
                        <div class="user-top-tabs">
                            ${userModules.map(m => `<a href="${m.link}" class="user-top-tab ${m.id === activeModule.id ? 'active' : ''}">${m.label}</a>`).join('')}
                        </div>
                        <div class="user-user-profile" onclick="window.location.href='user-profile.html'">
                            <div style="width:32px; height:32px; border-radius:50%; background:#1d1d1f; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.8rem;">${userInitials}</div>
                            <div style="font-size:0.85rem; font-weight:700; color:#1d1d1f;">${userName}</div>
                        </div>
                    `;
                    document.body.prepend(topNav);
                }
            }, 0);
        }
    },
    hideLoader: () => {
        const loader = document.getElementById('l-global-loader');
        if (loader) loader.remove();
    },

    showModal: (title, contentHTML, buttons = []) => {
        const overlay = document.createElement('div');
        overlay.style = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(5px); z-index:10000; display:flex; justify-content:center; align-items:center; opacity:0; transition:opacity 0.3s ease;';
        
        const modal = document.createElement('div');
        modal.style = 'background:white; border-radius:16px; width:90%; max-width:450px; box-shadow:0 25px 50px rgba(0,0,0,0.15); transform:scale(0.95); transition:transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow:hidden;';
        
        let buttonsHTML = '';
        buttons.forEach((btn, idx) => {
            const bg = btn.type === 'danger' ? '#ff3b30' : (btn.type === 'primary' ? '#0D9488' : '#f2f2f7');
            const color = btn.type === 'danger' || btn.type === 'primary' ? 'white' : '#1c1c1e';
            buttonsHTML += `<button id="modal-btn-${idx}" style="flex:1; padding:12px; border:none; border-radius:8px; background:${bg}; color:${color}; font-weight:600; cursor:pointer; font-family:inherit;">${btn.label}</button>`;
        });

        modal.innerHTML = `
            <div style="padding:24px;">
                <h3 style="margin:0 0 16px 0; font-size:1.2rem; color:#1c1c1e;">${title}</h3>
                <div style="font-size:0.9rem; color:#3a3a3c; line-height:1.5;">${contentHTML}</div>
            </div>
            <div style="padding:16px 24px; background:#f9f9f9; border-top:1px solid #e5e5ea; display:flex; gap:12px;">
                ${buttonsHTML}
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        buttons.forEach((btn, idx) => {
            document.getElementById(`modal-btn-${idx}`).addEventListener('click', () => {
                const closeFn = () => {
                    overlay.style.opacity = '0';
                    modal.style.transform = 'scale(0.95)';
                    setTimeout(() => overlay.remove(), 300);
                };
                if(btn.onClick) btn.onClick(closeFn);
                else closeFn();
            });
        });

        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
            modal.style.transform = 'scale(1)';
        });
    },

    header: (user, isInternalDashboard) => {
        const rel = window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('/') ? './' : '../';

        const userControls = user ? `
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-weight: 600;">${user.name}</span>
                <button onclick="auth.logout()" class="btn btn-outline" style="padding: 5px 12px; font-size: 0.8rem; border-color: var(--brand-teal); color: var(--brand-teal);">Logout</button>
            </div>
        ` : `<a href="${rel}auth/login.html" class="btn btn-primary" style="background: var(--brand-teal); border: none;">Login / Register</a>`;

        if (isInternalDashboard) {
            // Admin / Vendor minimal header
            return `
            <nav class="glass-nav">
                <div class="container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div></div> <!-- Spacer for sidebar -->
                    <div class="nav-actions">${userControls}</div>
                </div>
            </nav>`;
        }

        // Public / User E-commerce Header (PharmEasy Style)
        return `
        <header class="eco-header">
            <div class="container">
                <div class="eco-top">
                    <a href="${rel}index.html" class="logo" style="font-size: 1.8rem; color: var(--brand-teal); text-decoration: none;">
                        <span class="logo-icon" style="font-size: 2rem;">➕</span>
                        <span class="logo-text">Medi<span>Mitra</span></span>
                    </a>
                    
                    <div style="font-size: 0.85rem; color: var(--grey); min-width: 150px; cursor: pointer;">
                        <strong>Phaltan City ▾</strong><br>Quick Clinical Delivery
                    </div>

                    <div class="eco-search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" placeholder="Search from our 1,00,000+ products...">
                        <button class="eco-search-btn">Search</button>
                    </div>

                    <div class="eco-nav">
                        ${userControls}
                        <a href="${rel}user/cart-page.html">🛒 Cart</a>
                    </div>
                </div>

                <div class="eco-sec-nav">
                    <a href="${rel}user/medicines-page.html">All Medicines</a>
                    <a href="${rel}index.html#wellness">Wellness Offerings</a>
                    <a href="${rel}index.html#stores">Store Locator</a>
                    <a href="${rel}public/blog.html">Health Blogs</a>
                    <a href="${rel}user/user-prescriptions.html" style="color: var(--brand-teal); font-weight: 700;">Order via Prescription</a>
                </div>
            </div>
        </header>`;
    },

    // 2. Dashbard Sidebars
    sidebar: (role) => {
        const menus = {
            admin: [
                { icon: 'grid', label: 'Dashboard', link: 'admin-dashboard.html' },
                { icon: 'users', label: 'Vendors', link: 'admin-vendors.html' },
                { icon: 'user-cog', label: 'Users', link: 'admin-users.html' },
                { icon: 'settings', label: 'Settings', link: 'admin-system-settings.html' }
            ],
            vendor: [
                { icon: '🏠', label: 'Dashboard', link: 'vendor-dashboard.html' },
                { icon: '💊', label: 'Inventory', link: 'vendor-inventory.html' },
                { icon: '📦', label: 'Orders', link: 'vendor-orders.html' },
                { icon: 'refresh-ccw', label: 'Product Returns', link: 'vendor-returns.html' },
                { icon: '📄', label: 'Prescriptions', link: 'vendor-prescriptions.html' },
                { icon: '💰', label: 'Earnings', link: 'vendor-earnings.html' },
                { icon: '⚙️', label: 'Settings', link: 'vendor-settings.html' }
            ],
            user: [
                { icon: '🏠', label: 'Dashboard', link: 'user-dashboard.html' },
                { icon: '💊', label: 'Medicines', link: 'medicines-page.html' },
                { icon: '🛒', label: 'My Cart', link: 'cart-page.html' },
                { icon: '📦', label: 'My Orders', link: 'orders-page.html' },
                { icon: '📄', label: 'Prescriptions', link: 'user-prescriptions.html' },
                { icon: '👤', label: 'Profile', link: 'user-profile.html' }
            ],
            delivery: [
                { icon: '🏠', label: 'Dashboard', link: 'delivery-dashboard.html' },
                { icon: '📦', label: 'New Orders', link: 'delivery-orders.html' },
                { icon: '📋', label: 'Dispatch Queue', link: 'delivery-queue.html' },
                { icon: '📋', label: 'History', link: 'delivery-history.html' },
                { icon: '💰', label: 'Earnings', link: 'delivery-earnings.html' },
                { icon: '👤', label: 'Profile', link: 'delivery-profile.html' },
                { icon: '⚙️', label: 'Settings', link: 'delivery-settings.html' }
            ]
        };

        const currentMenu = menus[role] || [];
        const currentPath = window.location.pathname.split('/').pop();

        return `
            <aside class="sidebar">
                <div class="sidebar-header">
                    <span style="font-size: 1.5rem;">➕</span>
                    <span style="font-weight: 700; font-size: 1.25rem;">Medi<span>Mitra</span></span>
                </div>
                <nav class="sidebar-nav">
                    ${currentMenu.map(item => `
                        <a href="${item.link}" class="nav-item ${currentPath === item.link ? 'active' : ''}">
                            ${item.icon} ${item.label}
                        </a>
                    `).join('')}
                    <a href="#" onclick="auth.logout()" class="nav-item logout" style="margin-top: auto;">🚪 Logout</a>
                </nav>
            </aside>
        `;
    },



    // 5. Global Modal System (Liquid Design)
    showModal(title, content, actions = []) {
        const modalId = 'modal-' + Math.random().toString(36).substr(2, 9);
        const modal = document.createElement('div');
        modal.className = 'liquid-modal-overlay';
        modal.id = modalId;
        
        const actionButtons = actions.map(btn => `
            <button class="pill-button ${btn.type === 'danger' ? 'btn-danger' : (btn.type === 'primary' ? '' : 'btn-outline')}" 
                    onclick="(${btn.onClick.toString()})(); document.getElementById('${modalId}').remove();"
                    style="${btn.style || ''}">
                ${btn.label}
            </button>
        `).join('') || `<button class="pill-button" onclick="document.getElementById('${modalId}').remove()" style="width: 100%; justify-content: center;">Close</button>`;

        modal.innerHTML = `
            <div class="liquid-modal-card">
                <div class="modal-header">
                    <h2 style="font-weight: 800; font-size: 1.5rem; margin: 0;">${title}</h2>
                    <button class="modal-close" onclick="document.getElementById('${modalId}').remove()">×</button>
                </div>
                <div class="modal-body" style="padding: 24px 0;">
                    ${content}
                </div>
                <div class="modal-footer" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                    ${actionButtons}
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        setTimeout(() => modal.classList.add('show'), 10);
        return modalId;
    },

    // 6. Notifications system
    showNotification(message, type = 'success', duration = 4000) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icon = type === 'success' ? '✓' : '⚠';
        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
        
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('toast-out');
            setTimeout(() => toast.remove(), 400);
        }, duration);
    },

    /**
     * Custom Confirm Dialog — replaces browser native confirm()
     * Returns a Promise that resolves true/false
     * Usage: if (await components.confirm('Title', 'Message')) { ... }
     */
    confirm(title, message, confirmLabel = 'Confirm', cancelLabel = 'Cancel') {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.4);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);z-index:99999;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.25s ease;';
            
            const card = document.createElement('div');
            card.style.cssText = 'background:white;border-radius:24px;padding:32px;width:90%;max-width:400px;box-shadow:0 25px 60px rgba(0,0,0,0.15);transform:scale(0.92);transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);';
            
            const h3 = document.createElement('h3');
            h3.style.cssText = 'margin:0 0 8px 0;font-size:1.3rem;font-weight:800;color:#1d1d1f;';
            h3.textContent = title;
            
            const p = document.createElement('p');
            p.style.cssText = 'margin:0 0 28px 0;font-size:0.9rem;color:#6e6e73;font-weight:500;line-height:1.5;';
            p.textContent = message;
            
            const btnRow = document.createElement('div');
            btnRow.style.cssText = 'display:flex;gap:12px;';
            
            const cancelBtn = document.createElement('button');
            cancelBtn.style.cssText = 'flex:1;padding:14px;border:none;border-radius:14px;background:#f2f2f7;color:#1d1d1f;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:inherit;transition:background 0.2s;';
            cancelBtn.textContent = cancelLabel;
            cancelBtn.onmouseover = () => cancelBtn.style.background = '#e5e5ea';
            cancelBtn.onmouseout = () => cancelBtn.style.background = '#f2f2f7';
            
            const confirmBtn = document.createElement('button');
            confirmBtn.style.cssText = 'flex:1;padding:14px;border:none;border-radius:14px;background:#007aff;color:white;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:inherit;transition:background 0.2s;';
            confirmBtn.textContent = confirmLabel;
            confirmBtn.onmouseover = () => confirmBtn.style.background = '#0056b3';
            confirmBtn.onmouseout = () => confirmBtn.style.background = '#007aff';
            
            btnRow.appendChild(cancelBtn);
            btnRow.appendChild(confirmBtn);
            card.appendChild(h3);
            card.appendChild(p);
            card.appendChild(btnRow);
            overlay.appendChild(card);
            document.body.appendChild(overlay);
            
            requestAnimationFrame(() => { overlay.style.opacity = '1'; card.style.transform = 'scale(1)'; });
            
            const close = (result) => {
                overlay.style.opacity = '0';
                card.style.transform = 'scale(0.92)';
                setTimeout(() => { overlay.remove(); resolve(result); }, 250);
            };
            
            cancelBtn.onclick = () => close(false);
            confirmBtn.onclick = () => close(true);
            overlay.onclick = (e) => { if (e.target === overlay) close(false); };
        });
    },

    /**
     * Custom Prompt Dialog — replaces browser native prompt()
     * Returns a Promise that resolves with input value or null
     * Usage: const val = await components.prompt('Title', 'Message', 'default');
     */
    prompt(title, message, defaultValue = '', confirmLabel = 'Submit', cancelLabel = 'Cancel') {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.4);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);z-index:99999;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.25s ease;';
            
            const card = document.createElement('div');
            card.style.cssText = 'background:white;border-radius:24px;padding:32px;width:90%;max-width:420px;box-shadow:0 25px 60px rgba(0,0,0,0.15);transform:scale(0.92);transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);';
            
            const h3 = document.createElement('h3');
            h3.style.cssText = 'margin:0 0 8px 0;font-size:1.3rem;font-weight:800;color:#1d1d1f;';
            h3.textContent = title;
            
            const p = document.createElement('p');
            p.style.cssText = 'margin:0 0 20px 0;font-size:0.9rem;color:#6e6e73;font-weight:500;line-height:1.5;';
            p.textContent = message;
            
            const input = document.createElement('input');
            input.type = 'text';
            input.value = defaultValue;
            input.style.cssText = 'width:100%;padding:14px 18px;border:1px solid rgba(0,0,0,0.08);border-radius:14px;font-size:0.95rem;font-weight:600;font-family:inherit;background:#f9f9fb;outline:none;box-sizing:border-box;margin-bottom:24px;transition:border-color 0.2s;';
            input.onfocus = () => input.style.borderColor = '#007aff';
            input.onblur = () => input.style.borderColor = 'rgba(0,0,0,0.08)';
            
            const btnRow = document.createElement('div');
            btnRow.style.cssText = 'display:flex;gap:12px;';
            
            const cancelBtn = document.createElement('button');
            cancelBtn.style.cssText = 'flex:1;padding:14px;border:none;border-radius:14px;background:#f2f2f7;color:#1d1d1f;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:inherit;transition:background 0.2s;';
            cancelBtn.textContent = cancelLabel;
            cancelBtn.onmouseover = () => cancelBtn.style.background = '#e5e5ea';
            cancelBtn.onmouseout = () => cancelBtn.style.background = '#f2f2f7';
            
            const confirmBtn = document.createElement('button');
            confirmBtn.style.cssText = 'flex:1;padding:14px;border:none;border-radius:14px;background:#007aff;color:white;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:inherit;transition:background 0.2s;';
            confirmBtn.textContent = confirmLabel;
            confirmBtn.onmouseover = () => confirmBtn.style.background = '#0056b3';
            confirmBtn.onmouseout = () => confirmBtn.style.background = '#007aff';
            
            btnRow.appendChild(cancelBtn);
            btnRow.appendChild(confirmBtn);
            card.appendChild(h3);
            card.appendChild(p);
            card.appendChild(input);
            card.appendChild(btnRow);
            overlay.appendChild(card);
            document.body.appendChild(overlay);
            
            requestAnimationFrame(() => { overlay.style.opacity = '1'; card.style.transform = 'scale(1)'; input.focus(); input.select(); });
            
            const close = (result) => {
                overlay.style.opacity = '0';
                card.style.transform = 'scale(0.92)';
                setTimeout(() => { overlay.remove(); resolve(result); }, 250);
            };
            
            cancelBtn.onclick = () => close(null);
            confirmBtn.onclick = () => close(input.value);
            input.onkeydown = (e) => { if (e.key === 'Enter') close(input.value); if (e.key === 'Escape') close(null); };
            overlay.onclick = (e) => { if (e.target === overlay) close(null); };
        });
    },

    // 1. Sidebar Component
    footer: () => {
        const pathPrefix = window.location.pathname.includes('/public/') || window.location.pathname.includes('/user/') || window.location.pathname.includes('/auth/') ? '../public/' : 'public/';
        const userPrefix = window.location.pathname.includes('/public/') || window.location.pathname.includes('/user/') || window.location.pathname.includes('/auth/') ? '../user/' : 'user/';
        
        return `
        <footer style="padding: 60px 0 40px; background: var(--dark); color: white; font-size: 0.95rem;">
            <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 40px;">
                <div>
                    <h3 style="color: var(--primary); font-weight: 800; font-size: 1.5rem; margin-bottom: 20px;">MediMitra</h3>
                    <p style="color: #aaa; line-height: 1.6;">Your trusted clinical ecosystem for rapid, reliable, and verified medicine delivery.</p>
                </div>
                <div>
                    <h4 style="font-weight: 700; margin-bottom: 20px;">Platform</h4>
                    <ul style="list-style: none; padding: 0; line-height: 2;">
                        <li><a href="${userPrefix}compare.html" style="color: #aaa; text-decoration: none;">Compare Medicines Tool</a></li>
                        <li><a href="${pathPrefix}blog.html" style="color: #aaa; text-decoration: none;">Health Blogs & News</a></li>
                        <li><a href="${pathPrefix}partner-landing.html" style="color: #aaa; text-decoration: none;">Join as Pharmacy/Rider</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-weight: 700; margin-bottom: 20px;">Legal & Policies</h4>
                    <ul style="list-style: none; padding: 0; line-height: 2;">
                        <li><a href="${pathPrefix}refund-policy.html" style="color: #aaa; text-decoration: none;">Returns & Refunds</a></li>
                        <li><a href="${pathPrefix}privacy-policy.html" style="color: #aaa; text-decoration: none;">Privacy Policy</a></li>
                        <li><a href="${pathPrefix}terms-of-service.html" style="color: #aaa; text-decoration: none;">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="container" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; text-align: center; color: #777;">
                <p>&copy; 2026 MediMitra Clinical Ecosystem. All rights reserved.</p>
            </div>
        </footer>
        `;
    },

    liquidSidebar: (role, isSlim = false) => {
        const roleDashboards = { admin: 'admin-dashboard.html', vendor: 'vendor-dashboard.html', user: 'user-dashboard.html', delivery: 'delivery-dashboard.html' };
        
        // Detect current folder and compute prefix to correct role folder
        const pathParts = window.location.pathname.split('/');
        const currentFolder = pathParts[pathParts.length - 2] || '';
        const roleFolders = { admin: 'admin', vendor: 'vendor', user: 'user', delivery: 'delivery' };
        const expectedFolder = roleFolders[role] || '';
        const folderPrefix = (currentFolder !== expectedFolder && expectedFolder) ? `../${expectedFolder}/` : '';
        
        const homeLink = folderPrefix + (roleDashboards[role] || '../index.html');
        const currentPath = window.location.pathname.split('/').pop() || 'admin-dashboard.html';

        if (role === 'admin') {
            const adminModules = [
                {
                    id: 'dashboard', label: 'Dashboard', link: 'admin-dashboard.html',
                    items: [ { icon: 'grid', label: 'Home Dashboard', link: 'admin-dashboard.html' } ]
                },
                {
                    id: 'network', label: 'Network', link: 'admin-users.html',
                    items: [
                        { icon: 'users', label: 'User Directory', link: 'admin-users.html' },
                        { icon: 'hospital', label: 'Pharmacy Network', link: 'admin-vendors.html' },
                        { icon: 'truck', label: 'Delivery Fleet', link: 'admin-delivery-agents.html' },
                        { icon: 'check-square', label: 'Partner Approvals', link: 'admin-pharmacy-approvals.html' },
                        { icon: 'clipboard-list', label: 'KYC Verification', link: 'admin-kyc-verification.html' }
                    ]
                },
                {
                    id: 'catalog', label: 'Catalog', link: 'admin-medicines.html',
                    items: [
                        { icon: 'package', label: 'Medicine Inventory', link: 'admin-medicines.html' },
                        { icon: 'award', label: 'Brand Mgmt', link: 'admin-brand-management.html' },
                        { icon: 'layers', label: 'Categorization', link: 'admin-medicine-categorization.html' },
                        // { icon: 'upload-cloud', label: 'Bulk Operations', link: 'admin-bulk-operations.html' }
                    ]
                },
                {
                    id: 'system', label: 'System & Analytics', link: 'admin-system-pulse.html',
                    items: [
                        { icon: 'activity', label: 'System Pulse', link: 'admin-system-pulse.html' },
                        { icon: 'zap', label: 'Order Pulse', link: 'admin-order-pulse.html' },
                        { icon: 'bar-chart-2', label: 'Financials', link: 'admin-financials.html' },
                        { icon: 'pie-chart', label: 'Analytical Reports', link: 'admin-reports.html' },
                        { icon: 'ticket', label: 'Support Desk', link: 'admin-support-tickets.html' }
                    ]
                },
                {
                    id: 'settings', label: 'Settings & Security', link: 'admin-system-settings.html',
                    items: [
                        { icon: 'settings', label: 'General Settings', link: 'admin-system-settings.html' },
                        { icon: 'lock', label: 'Roles & Security', link: 'admin-roles-permissions.html' },
                        { icon: 'shield-check', label: 'Security Config', link: 'admin-security-settings.html' },
                        { icon: 'database', label: 'Backup & Recovery', link: 'admin-backup.html' },
                        { icon: 'file-text', label: 'System Logs', link: 'admin-system-logs.html' },
                        { icon: 'history', label: 'Activity Logs', link: 'admin-activity-logs.html' }
                    ]
                }
            ];

            let activeModule = adminModules[0];
            for (let mod of adminModules) {
                if (mod.items.some(i => i.link === currentPath) || currentPath === mod.link) {
                    activeModule = mod;
                    break;
                }
            }
            // Fallback: match by prefix pattern (e.g. admin-user-detail matches 'network' module)
            if (activeModule.id === 'dashboard' && currentPath !== 'admin-dashboard.html') {
                const prefixMap = { 'admin-user': 'network', 'admin-vendor': 'network', 'admin-delivery': 'network', 'admin-fleet': 'network', 'admin-kyc': 'network', 'admin-pharmacy': 'network', 'admin-medicine': 'catalog', 'admin-brand': 'catalog', 'admin-bulk': 'catalog', 'admin-prescription': 'catalog', 'admin-system': 'settings', 'admin-backup': 'settings', 'admin-role': 'settings', 'admin-security': 'settings', 'admin-activity': 'settings', 'admin-log': 'settings', 'admin-order': 'system', 'admin-financial': 'system', 'admin-report': 'system', 'admin-support': 'system', 'admin-newsletter': 'system', 'admin-blog': 'system', 'admin-banner': 'system', 'admin-audit': 'settings', 'admin-payout': 'system' };
                for (const [prefix, modId] of Object.entries(prefixMap)) {
                    if (currentPath.startsWith(prefix)) {
                        activeModule = adminModules.find(m => m.id === modId) || activeModule;
                        break;
                    }
                }
            }

            if (typeof window !== 'undefined' && document) {
                setTimeout(() => {
                    if (!document.getElementById('admin-top-nav')) {
                        const style = document.createElement('style');
                        style.innerHTML = `
                            #admin-top-nav {
                                position: fixed; top: 0; left: 0; width: 100%; height: 68px;
                                background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
                                border-bottom: 1px solid rgba(0,0,0,0.06); z-index: 10000;
                                display: flex; align-items: center; justify-content: space-between; padding: 0 32px;
                                box-shadow: 0 4px 24px rgba(0,0,0,0.02); box-sizing: border-box;
                            }
                            .admin-top-brand { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1.3rem; letter-spacing: -0.04em; color: #1d1d1f; text-decoration: none; }
                            .admin-top-brand span { color: #0D9488; }
                            .admin-top-tabs { display: flex; gap: 8px; }
                            .admin-top-tab {
                                padding: 10px 18px; border-radius: 12px; color: #6e6e73; font-weight: 700; font-size: 0.9rem;
                                text-decoration: none; transition: all 0.2s ease;
                            }
                            .admin-top-tab:hover { background: rgba(0,0,0,0.04); color: #1d1d1f; }
                            .admin-top-tab.active { background: #1d1d1f; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                            .squircle-card { height: 100vh !important; padding-top: 68px !important; margin-top: 0 !important; border-top-left-radius: 0; border-top-right-radius: 0; box-sizing: border-box; }
                            .liquid-sidebar { padding-top: 20px !important; padding-bottom: 20px !important; min-height: auto !important; height: 100%; }
                            .admin-user-profile { display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.03); padding: 6px 16px 6px 6px; border-radius: 100px; cursor:pointer; }
                            .admin-user-profile:hover { background: rgba(0,0,0,0.06); }
                        `;
                        document.head.appendChild(style);

                        const topNav = document.createElement('div');
                        topNav.id = 'admin-top-nav';
                        topNav.innerHTML = `
                            <div style="display:flex; align-items:center;">
                                <button class="mobile-sidebar-toggle" onclick="components.toggleSidebar()" style="background:none; border:none; color:#1d1d1f; cursor:pointer; display:none; align-items:center; justify-content:center; padding:8px; margin-right:4px;"><i data-lucide="menu"></i></button>
                                <a href="${homeLink}" class="admin-top-brand">➕ Medi<span>Mitra</span> <span style="font-size:0.7rem; background:#0D9488; color:white; padding:2px 6px; border-radius:6px; letter-spacing:0; margin-left:8px;">OS</span></a>
                            </div>
                            <div class="admin-top-tabs">
                                ${adminModules.map(m => `<a href="${m.link}" class="admin-top-tab ${m.id === activeModule.id ? 'active' : ''}">${m.label}</a>`).join('')}
                            </div>
                            <div class="admin-user-profile" onclick="window.location.href='admin-profile.html'">
                                <div style="width:32px; height:32px; border-radius:50%; background:#1d1d1f; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.8rem;">AD</div>
                                <div style="font-size:0.85rem; font-weight:700; color:#1d1d1f;">System Admin</div>
                            </div>
                        `;
                        document.body.prepend(topNav);
                        components.enableDragScroll(topNav.querySelector('.admin-top-tabs'));
                    }
                }, 0);
            }

            return `
                <div class="mobile-modules-list" style="display:none; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.15); padding-bottom: 12px; flex-direction:column; gap:4px;">
                    <h4 style="font-size:0.75rem; color:rgba(255,255,255,0.5); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.08em; font-weight:800; padding-left:12px;">Admin Modules</h4>
                    ${adminModules.map(m => `
                        <a href="${m.link}" class="liquid-nav-item ${m.id === activeModule.id ? 'active' : ''}" style="padding: 10px 14px; margin-bottom:0;">
                            ${m.label}
                        </a>
                    `).join('')}
                </div>
                <div class="sidebar-header" style="padding: 0 10px 12px 10px; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h3 style="font-weight: 800; font-size: 0.7rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.08em; margin: 0;">${activeModule.label} Options</h3>
                </div>
                <nav style="display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto;">
                    ${activeModule.items.map(item => `
                        <a href="${item.link}" class="liquid-nav-item ${currentPath === item.link ? 'active' : ''}">
                            <i data-lucide="${item.icon}"></i> ${item.label}
                        </a>
                    `).join('')}
                    <div style="flex: 1; min-height: 20px;"></div>
                    <a href="#" onclick="auth.logout()" class="liquid-nav-item" style="margin-top: auto; color: #ff3b30; background: rgba(255,59,48,0.05);">
                        <i data-lucide="log-out"></i> Logout Session
                    </a>
                </nav>
            `;
        }

        if (role === 'user') {
            const userModules = [
                {
                    id: 'shop', label: 'Storefront & Shop', link: 'user-dashboard.html',
                    extraLinks: ['medicine-details.html'],
                    items: [
                        { icon: 'home', label: 'Storefront Home', link: '../index.html' },
                        { icon: 'grid', label: 'Patient Dashboard', link: 'user-dashboard.html' },
                        { icon: 'pill', label: 'Browse Medicines', link: 'medicines-page.html' },
                        { icon: 'sparkles', label: 'Health Insights', link: 'user-recommendations.html' },
                        { icon: 'activity', label: 'Compare Medicines', link: 'compare.html' }
                    ]
                },
                {
                    id: 'orders', label: 'Orders & Checkout', link: 'cart-page.html',
                    extraLinks: ['order-details.html'],
                    items: [
                        { icon: 'shopping-cart', label: 'My Cart', link: 'cart-page.html' },
                        { icon: 'credit-card', label: 'Checkout Page', link: 'checkout-ui.html' },
                        { icon: 'clipboard', label: 'Order History', link: 'orders-page.html' },
                        { icon: 'map-pin', label: 'Live Order Tracking', link: 'order-tracking-map.html' },
                        { icon: 'refresh-ccw', label: 'Returns & Refunds', link: 'user-returns.html' }
                    ]
                },
                {
                    id: 'clinical', label: 'Clinical & Rx', link: 'user-prescriptions.html',
                    items: [
                        { icon: 'file-text', label: 'Prescription Hub', link: 'user-prescriptions.html' },
                        { icon: 'upload-cloud', label: 'Upload Rx Flow', link: 'user-prescription-flow.html' },
                        { icon: 'refresh-cw', label: 'Refill Manager', link: 'user-refills.html' }
                    ]
                },
                {
                    id: 'payments', label: 'Payments & Address', link: 'user-addresses.html',
                    items: [
                        { icon: 'wallet', label: 'Wallet & Addresses', link: 'user-addresses.html' },
                        { icon: 'history', label: 'Payment History', link: 'user-payment-history.html' },
                        { icon: 'lock', label: 'Payment Gateway', link: 'user-payment-gateway.html' },
                        { icon: 'heart', label: 'Product Wishlist', link: 'user-wishlist.html' }
                    ]
                },
                {
                    id: 'account', label: 'Account & Help', link: 'user-profile.html',
                    items: [
                        { icon: 'user', label: 'My Account', link: 'user-profile.html' },
                        { icon: 'bell', label: 'Notifications', link: 'user-notifications.html' },
                        { icon: 'help-circle', label: 'Help & Support', link: 'user-support-tickets.html' },
                        { icon: 'refresh-ccw', label: 'Product Returns', link: 'user-returns.html' }
                    ]
                }
            ];

            let activeModule = userModules[0];
            for (let mod of userModules) {
                const hasItem = mod.items.some(i => i.link === currentPath) || currentPath === mod.link;
                const hasExtra = mod.extraLinks && mod.extraLinks.includes(currentPath);
                if (hasItem || hasExtra) {
                    activeModule = mod;
                    break;
                }
            }

            if (typeof window !== 'undefined' && document) {
                setTimeout(() => {
                    if (!document.getElementById('user-top-nav')) {
                        const style = document.createElement('style');
                        style.innerHTML = `
                            #user-top-nav {
                                position: fixed; top: 0; left: 0; width: 100%; height: 68px;
                                background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
                                border-bottom: 1px solid rgba(0,0,0,0.06); z-index: 10000;
                                display: flex; align-items: center; justify-content: space-between; padding: 0 32px;
                                box-shadow: 0 4px 24px rgba(0,0,0,0.02); box-sizing: border-box;
                            }
                            .user-top-brand { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1.3rem; letter-spacing: -0.04em; color: #1d1d1f; text-decoration: none; }
                            .user-top-brand span { color: #0D9488; }
                            .user-top-tabs { display: flex; gap: 8px; }
                            .user-top-tab {
                                padding: 10px 18px; border-radius: 12px; color: #6e6e73; font-weight: 700; font-size: 0.9rem;
                                text-decoration: none; transition: all 0.2s ease;
                            }
                            .user-top-tab:hover { background: rgba(0,0,0,0.04); color: #1d1d1f; }
                            .user-top-tab.active { background: #1d1d1f; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                            .squircle-card { height: 100vh !important; padding-top: 68px !important; margin-top: 0 !important; border-top-left-radius: 0; border-top-right-radius: 0; box-sizing: border-box; }
                            .liquid-sidebar { padding-top: 20px !important; padding-bottom: 20px !important; min-height: auto !important; height: 100%; }
                            .user-user-profile { display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.03); padding: 6px 16px 6px 6px; border-radius: 100px; cursor:pointer; }
                            .user-user-profile:hover { background: rgba(0,0,0,0.06); }
                        `;
                        document.head.appendChild(style);

                        const userObj = auth.getUser();
                        const userName = userObj ? userObj.name : 'Patient';
                        const userInitials = userName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();

                        const topNav = document.createElement('div');
                        topNav.id = 'user-top-nav';
                        topNav.innerHTML = `
                            <div style="display:flex; align-items:center;">
                                <button class="mobile-sidebar-toggle" onclick="components.toggleSidebar()" style="background:none; border:none; color:#1d1d1f; cursor:pointer; display:none; align-items:center; justify-content:center; padding:8px; margin-right:4px;"><i data-lucide="menu"></i></button>
                                <a href="${homeLink}" class="user-top-brand">➕ Medi<span>Mitra</span> <span style="font-size:0.7rem; background:#0D9488; color:white; padding:2px 6px; border-radius:6px; letter-spacing:0; margin-left:8px;">PATIENT</span></a>
                            </div>
                            <div class="user-top-tabs">
                                ${userModules.map(m => `<a href="${m.link}" class="user-top-tab ${m.id === activeModule.id ? 'active' : ''}">${m.label}</a>`).join('')}
                            </div>
                            <div class="user-user-profile" onclick="window.location.href='user-profile.html'">
                                <div style="width:32px; height:32px; border-radius:50%; background:#1d1d1f; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.8rem;">${userInitials}</div>
                                <div style="font-size:0.85rem; font-weight:700; color:#1d1d1f;">${userName}</div>
                            </div>
                        `;
                        document.body.prepend(topNav);
                        components.enableDragScroll(topNav.querySelector('.user-top-tabs'));
                    }
                }, 0);
            }

            return `
                <div class="mobile-modules-list" style="display:none; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.15); padding-bottom: 12px; flex-direction:column; gap:4px;">
                    <h4 style="font-size:0.75rem; color:rgba(255,255,255,0.5); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.08em; font-weight:800; padding-left:12px;">Modules</h4>
                    ${userModules.map(m => `
                        <a href="${m.link}" class="liquid-nav-item ${m.id === activeModule.id ? 'active' : ''}" style="padding: 10px 14px; margin-bottom:0;">
                            ${m.label}
                        </a>
                    `).join('')}
                </div>
                <div class="sidebar-header" style="padding: 0 10px 12px 10px; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h3 style="font-weight: 800; font-size: 0.7rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.08em; margin: 0;">${activeModule.label}</h3>
                </div>
                <nav style="display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto;">
                    ${activeModule.items.map(item => `
                        <a href="${item.link}" class="liquid-nav-item ${currentPath === item.link ? 'active' : ''}">
                            <i data-lucide="${item.icon}"></i> ${item.label}
                        </a>
                    `).join('')}
                    <div style="flex: 1; min-height: 20px;"></div>
                    <a href="#" onclick="auth.logout()" class="liquid-nav-item" style="margin-top: auto; color: #ff3b30; background: rgba(255,59,48,0.05);">
                        <i data-lucide="log-out"></i> Logout Session
                    </a>
                </nav>
            `;
        }

        // ── VENDOR ROLE ──────────────────────────────────────────────────────
        if (role === 'vendor') {
            const vendorModules = [
                {
                    id: 'ops', label: 'Pharmacy Ops', link: 'vendor-dashboard.html',
                    items: [
                        { icon: 'grid', label: 'Dashboard', link: 'vendor-dashboard.html' },
                        { icon: 'shopping-bag', label: 'Recent Orders', link: 'vendor-orders.html' },
                        { icon: 'file-text', label: 'Prescription Review', link: 'vendor-prescriptions.html' },
                        { icon: 'refresh-ccw', label: 'Product Returns', link: 'vendor-returns.html' },
                        { icon: 'check-circle', label: 'Approval Tracker', link: 'vendor-approval-tracker.html' }
                    ]
                },
                {
                    id: 'inventory', label: 'Inventory', link: 'vendor-inventory.html',
                    items: [
                        { icon: 'plus-circle', label: 'Add Medicine', link: 'vendor-medicine-form.html' },
                        { icon: 'package', label: 'My Inventory', link: 'vendor-inventory.html' },
                        { icon: 'layers', label: 'Catalog Config', link: 'vendor-catalog-config.html' },
                        { icon: 'alert-triangle', label: 'Stock Alerts', link: 'vendor-inventory-alerts.html' },
                        { icon: 'clipboard', label: 'Inventory Reports', link: 'vendor-inventory-reports.html' }
                    ]
                },
                {
                    id: 'analytics', label: 'Analytics', link: 'vendor-analytical-reports.html',
                    items: [
                        { icon: 'bar-chart-2', label: 'Analytical Reports', link: 'vendor-analytical-reports.html' },
                        { icon: 'file-text', label: 'Sales Reports', link: 'vendor-reports.html' },
                        { icon: 'percent', label: 'Commission Summary', link: 'vendor-commission-summary.html' }
                    ]
                },
                {
                    id: 'account', label: 'Account', link: 'vendor-profile.html',
                    items: [
                        { icon: 'user', label: 'Pharmacy Profile', link: 'vendor-profile.html' },
                        { icon: 'dollar-sign', label: 'Earnings & Payouts', link: 'vendor-earnings.html' },
                        { icon: 'credit-card', label: 'Payout Manager', link: 'vendor-payout-manager.html' },
                        { icon: 'shield', label: 'KYC Verification', link: 'vendor-kyc.html' },
                        { icon: 'settings', label: 'Settings', link: 'vendor-settings.html' }
                    ]
                }
            ];

            let activeModule = vendorModules[0];
            for (let mod of vendorModules) {
                if (mod.items.some(i => i.link === currentPath) || currentPath === mod.link) {
                    activeModule = mod;
                    break;
                }
            }

            if (typeof window !== 'undefined' && document) {
                setTimeout(() => {
                    if (!document.getElementById('vendor-top-nav')) {
                        const style = document.createElement('style');
                        style.innerHTML = `
                            #vendor-top-nav {
                                position: fixed; top: 0; left: 0; width: 100%; height: 68px;
                                background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
                                border-bottom: 1px solid rgba(0,0,0,0.06); z-index: 10000;
                                display: flex; align-items: center; justify-content: space-between; padding: 0 32px;
                                box-shadow: 0 4px 24px rgba(0,0,0,0.04); box-sizing: border-box;
                            }
                            .vendor-top-brand { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1.3rem; letter-spacing: -0.04em; color: #1d1d1f; text-decoration: none; }
                            .vendor-top-brand span { color: #0D9488; }
                            .vendor-top-tabs { display: flex; gap: 4px; }
                            .vendor-top-tab {
                                padding: 8px 14px; border-radius: 10px; color: #6e6e73; font-weight: 700; font-size: 0.8rem;
                                text-decoration: none; transition: all 0.2s ease;
                            }
                            .vendor-top-tab:hover { background: rgba(0,0,0,0.04); color: #1d1d1f; }
                            .vendor-top-tab.active { background: #0D9488; color: white; box-shadow: 0 4px 12px rgba(13,148,136,0.2); }
                            .squircle-card { height: calc(100vh - 68px) !important; margin-top: 68px !important; border-top-left-radius: 0; border-top-right-radius: 0; }
                            .liquid-sidebar { padding-top: 20px !important; padding-bottom: 20px !important; min-height: auto !important; height: 100%; }
                            .vendor-user-profile { display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.03); padding: 5px 14px 5px 5px; border-radius: 100px; cursor:pointer; margin-left: 12px; }
                            .vendor-user-profile:hover { background: rgba(0,0,0,0.06); }
                        `;
                        document.head.appendChild(style);

                        const userObj = (typeof auth !== 'undefined') ? auth.getUser() : null;
                        const vendorName = userObj ? userObj.name : 'Pharmacy';
                        const vendorInitials = vendorName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();

                        const topNav = document.createElement('div');
                        topNav.id = 'vendor-top-nav';
                        topNav.innerHTML = `
                            <a href="${homeLink}" class="vendor-top-brand">➕ Medi<span>Mitra</span> <span style="font-size:0.7rem; background:#0D9488; color:white; padding:2px 6px; border-radius:6px; letter-spacing:0; margin-left:8px;">VENDOR</span></a>
                            <div class="vendor-top-tabs">
                                ${vendorModules.map(m => `<a href="${folderPrefix}${m.link}" class="vendor-top-tab ${m.id === activeModule.id ? 'active' : ''}">${m.label}</a>`).join('')}
                            </div>
                            <div class="vendor-user-profile" onclick="window.location.href='vendor-profile.html'">
                                <div style="width:32px; height:32px; border-radius:50%; background:#115E59; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.8rem;">${vendorInitials}</div>
                                <div style="font-size:0.85rem; font-weight:700; color:#1d1d1f;">${vendorName}</div>
                            </div>
                        `;
                        document.body.prepend(topNav);
                    }
                }, 0);
            }

            return `
                <div class="sidebar-header" style="padding: 0 10px 12px 10px; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.15);">
                    <h3 style="font-weight: 800; font-size: 0.7rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.08em; margin: 0;">${activeModule.label}</h3>
                </div>
                <nav style="display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto;">
                    ${activeModule.items.map(item => `
                        <a href="${folderPrefix}${item.link}" class="liquid-nav-item ${currentPath === item.link ? 'active' : ''}">
                            <i data-lucide="${item.icon}"></i> ${item.label}
                        </a>
                    `).join('')}
                    <div style="flex: 1; min-height: 20px;"></div>
                    <a href="#" onclick="auth.logout()" class="liquid-nav-item" style="margin-top: auto; color: #ff6b6b; background: rgba(255,59,48,0.1);">
                        <i data-lucide="log-out"></i> Logout Session
                    </a>
                </nav>
            `;
        }

        // ── DELIVERY / fallback menus ──────────────────────────────────────
        const menus = {
    delivery: [
        { section: 'DELIVERY OPS', items: [
            { icon: 'home', label: 'Dashboard', link: 'delivery-dashboard.html' },
            { icon: 'box', label: 'New Orders', link: 'delivery-orders.html' },
            { icon: 'list', label: 'Dispatch Queue', link: 'delivery-queue.html' },
            { icon: 'clock', label: 'History', link: 'delivery-history.html' },
            { icon: 'dollar-sign', label: 'Earnings', link: 'delivery-earnings.html' }
        ]},
        { section: 'ACCOUNT', items: [
            { icon: 'user', label: 'Profile', link: 'delivery-profile.html' },
            { icon: 'settings', label: 'Settings', link: 'delivery-settings.html' }
        ]}
    ],
    user: [
        { section: 'SHOP', items: [
            { icon: 'home', label: 'Storefront Home', link: '../index.html' },
            { icon: 'grid', label: 'Patient Dashboard', link: 'user-dashboard.html' },
            { icon: 'pill', label: 'Browse Medicines', link: 'medicines-page.html' },
            { icon: 'sparkles', label: 'Health Insights', link: 'user-recommendations.html' },
            { icon: 'file-text', label: 'Upload Rx', link: 'user-prescriptions.html' }
        ]},
        { section: 'ORDERS', items: [
            { icon: 'shopping-cart', label: 'My Cart', link: 'cart-page.html' },
            { icon: 'clipboard', label: 'Order History', link: 'orders-page.html' },
            { icon: 'refresh-ccw', label: 'My Returns', link: 'user-returns.html' },
            { icon: 'heart', label: 'Wishlist', link: 'user-wishlist.html' }
        ]},
        { section: 'PROFILE', items: [
            { icon: 'user', label: 'My Account', link: 'user-profile.html' },
            { icon: 'wallet', label: 'Payments & Address', link: 'user-addresses.html' },
            { icon: 'help-circle', label: 'Help & Support', link: 'user-support-tickets.html' },
            { icon: 'bell', label: 'Notifications', link: 'user-notifications.html' }
        ]}
    ]
};

        const currentMenu = menus[role] || [];
        
        if (isSlim) {
            let slimHTML = `
                <div style="margin-bottom: 30px;">
                    <a href="${homeLink}" style="text-decoration: none;">
                        <div style="width:40px; height:40px; background:var(--primary); color:white; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem;">M</div>
                    </a>
                </div>
                <nav style="display: flex; flex-direction: column; gap: 16px; align-items: center; width: 100%;">
            `;
            currentMenu.forEach(group => {
                group.items.forEach(item => {
                    const isActive = currentPath === item.link;
                    slimHTML += `
                        <a href="${item.link}" class="nav-item-slim ${isActive ? 'active' : ''}" title="${item.label}">
                            <i data-lucide="${item.icon}"></i>
                        </a>
                    `;
                });
            });
            slimHTML += `
                <a href="#" onclick="auth.logout()" class="nav-item-slim" title="Logout" style="margin-top: auto; color: #ff3b30;">
                    <i data-lucide="log-out"></i>
                </a>
            </nav>`;
            return slimHTML;
        }

        let sidebarHTML = `
            <div class="sidebar-header" style="padding: 0 10px 30px 10px;">
                <a href="${homeLink}" style="text-decoration: none; color: inherit;">
                    <h2 style="font-weight: 800; font-size: 1.5rem; letter-spacing: -0.05em; color: #ffffff;">MediMitra</h2>
                </a>
            </div>
            <nav style="display: flex; flex-direction: column; gap: 20px;">
        `;
        currentMenu.forEach(group => {
            sidebarHTML += `
                <div class="nav-group">
                    <div style="font-size: 0.65rem; font-weight: 800; color: rgba(255,255,255,0.5); letter-spacing: 0.08em; padding: 0 14px 8px 14px;">${group.section}</div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        ${group.items.map(item => `
                            <a href="${item.link}" class="liquid-nav-item ${currentPath === item.link ? 'active' : ''}">
                                <i data-lucide="${item.icon}"></i> ${item.label}
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        });
        sidebarHTML += `
                <a href="#" onclick="auth.logout()" class="liquid-nav-item" style="margin-top: 20px; color: #ff3b30;">
                    <i data-lucide="log-out"></i> Logout
                </a>
            </nav>
        `;
        return sidebarHTML;
    }
};

// Ensure global accessibility
window.components = components;
