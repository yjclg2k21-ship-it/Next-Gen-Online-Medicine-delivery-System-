/**
 * MediMitra Dashboard Data Integrator
 * Fetches real data from PHP API and populates Liquid UI components
 */

class DashboardIntegrator {
    constructor(role) {
        this.role = role;
        this.init();
    }

    async init() {
        console.log(`Initializing ${this.role} Dashboard Data...`);
        try {
            if (this.role === 'admin') {
                await this.loadAdminData();
            } else if (this.role === 'vendor') {
                await this.loadVendorData();
            } else if (this.role === 'user') {
                await this.loadUserData();
            }
        } catch (error) {
            console.error('Failed to load dashboard data:', error);
        }
    }

    async loadAdminData() {
        // 1. Fetch Dashboard Stats
        try {
            const data = await api.get('/admin/dashboard');
            if (data && data.success) {
                const stats = data.data.stats;
                this.updateElement('total-revenue', `₹${stats.total_revenue.toLocaleString()}`);
                this.updateElement('total-vendors', stats.total_vendors);
                this.updateElement('pending-reviews', stats.pending_reviews);
                this.updateElement('active-users', stats.total_users);
            }
        } catch (e) {
            console.error('Admin Stats fetch failed:', e);
            this.updateElement('total-revenue', '-');
            this.updateElement('total-vendors', '-');
            this.updateElement('pending-reviews', '-');
            this.updateElement('active-users', '-');
        }

        // 2. Fetch Pulse (Real-time)
        try {
            const pulseData = await api.get('/admin/pulse');
            if (pulseData && pulseData.success) {
                const pulse = pulseData.data.pulse;
                this.updateElement('pulse-latency', pulse.latency);
                this.updateElement('pulse-uptime', pulse.api_uptime);
                this.updateElement('pulse-orders', pulse.order_velocity);
            }
        } catch (e) {
            console.error('Admin Pulse fetch failed:', e);
            this.updateElement('pulse-latency', 'N/A');
            this.updateElement('pulse-uptime', 'N/A');
            this.updateElement('pulse-orders', 'N/A');
        }
    }

    async loadVendorData() {
        try {
            const data = await api.get('/vendor/dashboard');
            if (data && data.success) {
                const stats = data.data;
                this.updateElement('daily-revenue', `₹${stats.daily_revenue.toLocaleString()}`);
                this.updateElement('pending-orders', stats.pending_orders);
                this.updateElement('low-stock-count', stats.inventory_alerts);
            }
        } catch (e) {
            console.error('Vendor Stats fetch failed:', e);
            this.updateElement('daily-revenue', '-');
            this.updateElement('pending-orders', '-');
            this.updateElement('low-stock-count', '-');
        }
    }

    async loadUserData() {
        // Fetch User Dashboard Info
        try {
            const res = await api.get('/user/profile');
            if (res && res.success) {
                const user = res.data.user;
                this.updateElement('helloName', user.name.split(' ')[0]);
                this.updateElement('userName', user.name);

                // Sync name with base user object in local/session storage
                if (typeof auth !== 'undefined') {
                    const storedUser = auth.getUser();
                    if (storedUser && user.name) {
                        storedUser.name = user.name;
                        auth.updateUser(storedUser);
                    }
                }
            }
        } catch (e) {
            console.error('User Profile fetch failed:', e);
            this.updateElement('helloName', 'User');
            this.updateElement('userName', 'User');
        }

        try {
            const wallet = await api.get('/user/wallet');
            if (wallet && wallet.success) {
                this.updateElement('user-balance', `₹${wallet.data.balance.toLocaleString()}`);
            }
        } catch (e) {
            console.error('User Wallet fetch failed:', e);
            this.updateElement('user-balance', '-');
        }

        try {
            const orders = await api.get('/orders');
            if (orders && orders.success) {
                const pending = orders.data.filter(o => o.status !== 'delivered' && o.status !== 'cancelled').length;
                this.updateElement('user-orders', pending);
            }
        } catch (e) {
            console.error('User Orders fetch failed:', e);
            this.updateElement('user-orders', '-');
        }
    }

    async loadDeliveryData() {
        try {
            const queue = await api.get('/delivery/dispatch/queue');
            if (queue && queue.success) {
                this.updateElement('delivery-pending', queue.data.length);
            }
        } catch (e) {
            console.error('Delivery Queue fetch failed:', e);
            this.updateElement('delivery-pending', '-');
        }
        
        try {
            const history = await api.get('/delivery/history');
            if (history && history.success) {
                this.updateElement('delivery-count', history.data.length);
            }
        } catch (e) {
            console.error('Delivery History fetch failed:', e);
            this.updateElement('delivery-count', '-');
        }
    }

    updateElement(id, value) {
        const el = document.getElementById(id);
        if (el) {
            // Add a small animation when value changes
            el.style.opacity = '0';
            setTimeout(() => {
                el.textContent = value;
                el.style.opacity = '1';
                el.style.transition = 'opacity 0.5s ease';
            }, 300);
        }
    }
}

// Global instances for specific pages
if (window.location.pathname.includes('admin-dashboard')) {
    document.addEventListener('DOMContentLoaded', () => new DashboardIntegrator('admin'));
} else if (window.location.pathname.includes('vendor-dashboard')) {
    document.addEventListener('DOMContentLoaded', () => new DashboardIntegrator('vendor'));
} else if (window.location.pathname.includes('user-dashboard')) {
    document.addEventListener('DOMContentLoaded', () => new DashboardIntegrator('user'));
} else if (window.location.pathname.includes('delivery-dashboard')) {
    document.addEventListener('DOMContentLoaded', () => new DashboardIntegrator('delivery'));
}
