/**
 * MediMitra API Gateway
 * Final Fixed Version for XAMPP Subfolders
 */

const API_CONFIG = {
    BASE_URL: (function() {
        const path = window.location.pathname;
        const origin = window.location.origin;

        /**
         * Encode each path segment individually so spaces and special characters
         * in directory names (e.g. "Next Gen Online Medicine Delivery System")
         * are percent-encoded without double-encoding slashes.
         */
        function encodePath(rawPath) {
            return rawPath.split('/').map(seg => encodeURIComponent(decodeURIComponent(seg))).join('/');
        }
        
        // If the URL contains '/frontend/', we are likely in a XAMPP subfolder structure
        if (path.includes('/frontend/')) {
            const projectDir = path.substring(0, path.indexOf('/frontend/'));
            return origin + encodePath(projectDir) + '/api';
        } 
        
        // If we are at the root or using the custom router where /frontend/ is hidden
        // We need to find the project root. For the built-in server (localhost:8000), it's just /api
        if (window.location.port === '8000' || window.location.hostname === 'localhost') {
            // Check if we are in a subfolder even without /frontend/
            // e.g., /ProjectName/user/dashboard.html
            // We'll assume the first segment is the project name if it's not a known area
            const segments = path.split('/').filter(s => s.length > 0);
            const knownAreas = ['user', 'admin', 'vendor', 'delivery', 'auth', 'assets', 'guest'];
            
            if (segments.length > 0 && !knownAreas.includes(segments[0])) {
                return origin + '/' + encodeURIComponent(decodeURIComponent(segments[0])) + '/api';
            }
            return origin + '/api';
        }

        return origin + '/api';
    })(),
    HEADERS: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
};

console.log("MediMitra API FINAL Endpoint:", API_CONFIG.BASE_URL);

const api = {
    BASE_URL: API_CONFIG.BASE_URL,
    headers: API_CONFIG.HEADERS,
    async request(method, endpoint, body = null) {
        const url = `${this.BASE_URL}${endpoint}`;
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        
        const headers = { ...this.headers };
        if (token) headers['Authorization'] = `Bearer ${token}`;

        // If body is NOT FormData, stringify it and set Content-Type
        if (body && !(body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            body = JSON.stringify(body);
        } else if (body instanceof FormData) {
            // When using FormData, browser automatically sets multipart/form-data with boundary
            delete headers['Content-Type'];
        }

        const options = {
            method,
            headers,
            body
        };

        try {
            const response = await fetch(url, options);
            let data;
            const contentType = response.headers.get("content-type");
            
            if (contentType && contentType.includes("application/json")) {
                data = await response.json();
            } else {
                const rawText = await response.text();
                console.error("Non-JSON Response detected:", rawText);
                throw new Error("Server returned an invalid format (Expected JSON).");
            }
            
            if (!response.ok) {
                // If unauthorized, redirect to login
                if (response.status === 401) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    const loginPage = window.location.pathname.includes('/admin/') ? 'admin-login.html' : 'login.html';
                    window.location.href = '../auth/' + loginPage;
                }
                throw new Error(data.message || data.error || 'Pharmaceutical API error occurred');
            }
            return data;
        } catch (error) {
            console.error("API Error:", error.message);
            // Suppress global toasts for common dashboard background syncs
            if (!endpoint.includes('/dashboard')) {
                if (window.components && window.components.showError) {
                    window.components.showError(error.message);
                }
            }
            throw error;
        }
    },

    get(endpoint) { return this.request('GET', endpoint); },
    post(endpoint, body) { return this.request('POST', endpoint, body); },
    put(endpoint, body) { return this.request('PUT', endpoint, body); },
    delete(endpoint) { return this.request('DELETE', endpoint); }
};
