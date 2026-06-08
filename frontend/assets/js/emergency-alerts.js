/**
 * MediMitra Emergency Alerts Module
 * Shared audio/visual alert system for emergency order handling across all panels.
 * Uses Web Audio API for tone generation with visual overlay fallback.
 */

const EmergencyAlerts = {
    // Internal state
    _audioContext: null,
    _currentOscillator: null,
    _currentGainNode: null,
    _repeatingInterval: null,
    _overlayElement: null,
    _pollingIntervals: {},
    _pollIdCounter: 0,
    _audioReady: false,       // true after first user gesture
    _pendingOptions: null,    // queued tone to play after gesture

    /**
     * Call once on first user gesture to unlock AudioContext.
     * @private
     */
    _initAudioContext() {
        if (this._audioContext) return;
        try {
            this._audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this._audioReady = true;
            // Play any queued tone immediately after unlock
            if (this._pendingOptions !== null) {
                const { frequency, duration, orderData } = this._pendingOptions;
                this._pendingOptions = null;
                this._tryPlayTone(frequency || 880, duration || 3, orderData);
            }
        } catch (e) {
            // Web Audio API not supported
        }
    },

    // =========================================================================
    // AUDIO ALERT SYSTEM
    // =========================================================================

    /**
     * Play a distinct 3-second emergency tone using Web Audio API.
     * Falls back to full-screen visual overlay if audio fails.
     * @param {Object} options - Configuration options
     * @param {number} options.frequency - Base frequency in Hz (default: 880)
     * @param {number} options.duration - Duration in seconds (default: 3)
     * @param {Object} options.orderData - Order data for overlay fallback
     */
    playEmergencyTone(options = {}) {
        const frequency = options.frequency || 880;
        const duration  = options.duration  || 3;
        const orderData = options.orderData  || null;

        // Always show visual overlay for emergency — audio is best-effort
        this.showFullScreenOverlay(orderData);

        if (!this._audioReady) {
            // Queue audio for when user interacts
            this._pendingOptions = { frequency, duration, orderData };
            return;
        }

        this._tryPlayTone(frequency, duration, orderData);
    },

    /**
     * Attempt to play a tone — falls back silently if blocked.
     * @private
     */
    _tryPlayTone(frequency, duration, orderData) {
        try {
            const ctx = this._audioContext;
            if (!ctx) return;
            const contextReady = ctx.state === 'suspended' ? ctx.resume() : Promise.resolve();
            contextReady.then(() => {
                this._playTonePattern(frequency, duration);
            }).catch(() => {});
        } catch (e) {}
    },

    /**
     * Generate a pulsing urgent tone pattern (high-low-high) over the given duration.
     * @private
     */
    _playTonePattern(frequency, duration) {
        const ctx = this._audioContext;
        const now = ctx.currentTime;

        // Stop any currently playing tone
        this.stopAudio();

        // Create gain node for volume envelope
        const gainNode = ctx.createGain();
        gainNode.connect(ctx.destination);
        gainNode.gain.setValueAtTime(0, now);

        // Create oscillator with square wave for urgency
        const oscillator = ctx.createOscillator();
        oscillator.type = 'square';
        oscillator.connect(gainNode);

        // Create pulsing pattern: 6 rapid beeps over 3 seconds
        const beepDuration = duration / 6;
        for (let i = 0; i < 6; i++) {
            const beepStart = now + (i * beepDuration);
            const beepEnd = beepStart + (beepDuration * 0.7);

            // Alternate between high and low frequency for urgency
            const freq = i % 2 === 0 ? frequency : frequency * 0.75;
            oscillator.frequency.setValueAtTime(freq, beepStart);

            // Volume envelope per beep
            gainNode.gain.setValueAtTime(0.6, beepStart);
            gainNode.gain.setValueAtTime(0, beepEnd);
        }

        oscillator.start(now);
        oscillator.stop(now + duration);

        this._currentOscillator = oscillator;
        this._currentGainNode = gainNode;

        // Clean up references when done
        oscillator.onended = () => {
            this._currentOscillator = null;
            this._currentGainNode = null;
        };
    },

    /**
     * Stop current audio playback immediately.
     */
    stopAudio() {
        if (this._currentOscillator) {
            try {
                this._currentOscillator.stop();
            } catch (e) {
                // Already stopped
            }
            this._currentOscillator = null;
        }
        if (this._currentGainNode) {
            this._currentGainNode.disconnect();
            this._currentGainNode = null;
        }
        this.stopRepeatingAlert();
    },

    /**
     * Start repeating the emergency tone at a given interval until acknowledged.
     * @param {number} intervalMs - Interval between tones in milliseconds (default: 10000)
     * @param {Object} options - Options passed to playEmergencyTone
     */
    startRepeatingAlert(intervalMs = 10000, options = {}) {
        // Play immediately first
        this.playEmergencyTone(options);

        // Then repeat at interval
        this._repeatingInterval = setInterval(() => {
            this.playEmergencyTone(options);
        }, intervalMs);
    },

    /**
     * Stop the repeating alert interval.
     */
    stopRepeatingAlert() {
        if (this._repeatingInterval) {
            clearInterval(this._repeatingInterval);
            this._repeatingInterval = null;
        }
    },

    // =========================================================================
    // VISUAL ALERT SYSTEM
    // =========================================================================

    /**
     * Show a full-screen red overlay with emergency order details.
     * Used as fallback when audio playback fails.
     * @param {Object} orderData - Order information to display
     * @param {string} orderData.orderId - Order ID
     * @param {string} orderData.customerName - Customer name
     * @param {string} orderData.items - Order items description
     * @param {string} orderData.slaRemaining - Remaining SLA time
     */
    showFullScreenOverlay(orderData) {
        // Remove existing overlay if present
        this.hideOverlay();

        const toast = document.createElement('div');
        toast.id = 'emergency-alert-overlay';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 340px;
            background: linear-gradient(135deg, #b80000, #ff3b30);
            color: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(255,59,48,0.5), 0 2px 8px rgba(0,0,0,0.3);
            z-index: 99999;
            padding: 20px 22px;
            transform: translateX(400px);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            animation: emergency-border-pulse 0.8s ease-in-out infinite;
            border: 2px solid rgba(255,255,255,0.3);
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
        `;

        const orderId = orderData?.orderId || '';
        const customer = orderData?.customerName || 'Patient';
        const items = orderData?.items || 'Emergency Order';
        const sla = orderData?.slaRemaining || '--:--';

        toast.innerHTML = `
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div style="font-size:1.6rem;flex-shrink:0;animation:emergency-pulse-icon 0.8s ease-in-out infinite;">🚨</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:800;font-size:0.95rem;letter-spacing:0.06em;margin-bottom:4px;">EMERGENCY ORDER${orderId ? ' #' + orderId : ''}</div>
                    <div style="font-size:0.82rem;opacity:0.92;margin-bottom:2px;">👤 ${customer}</div>
                    <div style="font-size:0.82rem;opacity:0.92;margin-bottom:2px;">💊 ${items}</div>
                    <div style="font-size:0.82rem;opacity:0.92;">⏱ ${sla} remaining</div>
                </div>
                <button onclick="EmergencyAlerts.hideOverlay();EmergencyAlerts._initAudioContext();EmergencyAlerts.stopRepeatingAlert();"
                    style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.35)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                    title="Dismiss">✕</button>
            </div>
            <button onclick="EmergencyAlerts._initAudioContext();EmergencyAlerts.hideOverlay();EmergencyAlerts.stopRepeatingAlert();"
                style="margin-top:14px;width:100%;background:rgba(255,255,255,0.95);color:#cc0000;border:none;padding:10px;border-radius:10px;font-weight:700;font-size:0.88rem;cursor:pointer;transition:all 0.2s;"
                onmouseover="this.style.background='#fff';this.style.transform='scale(1.02)'"
                onmouseout="this.style.background='rgba(255,255,255,0.95)';this.style.transform='scale(1)'">
                🔔 Acknowledge & Dismiss
            </button>
        `;

        document.body.appendChild(toast);
        this._overlayElement = toast;

        // Slide in
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
            });
        });

        // Auto dismiss after 30 seconds
        this._overlayTimeout = setTimeout(() => this.hideOverlay(), 30000);
    },

    /**
     * Dismiss the full-screen overlay.
     */
    hideOverlay() {
        if (this._overlayTimeout) {
            clearTimeout(this._overlayTimeout);
            this._overlayTimeout = null;
        }
        if (this._overlayElement) {
            this._overlayElement.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (this._overlayElement && this._overlayElement.parentNode) {
                    this._overlayElement.parentNode.removeChild(this._overlayElement);
                }
                this._overlayElement = null;
            }, 400);
        }
        // Also remove by ID in case reference was lost
        const existing = document.getElementById('emergency-alert-overlay');
        if (existing && existing !== this._overlayElement) {
            existing.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (existing.parentNode) existing.parentNode.removeChild(existing);
            }, 400);
        }
    },

    /**
     * Apply CSS pulsing animation to an element at a configurable rate.
     * @param {HTMLElement} element - DOM element to animate
     * @param {number} pulsesPerSecond - Pulse rate (default: 1)
     */
    applyPulseAnimation(element, pulsesPerSecond = 1) {
        if (!element) return;

        if (pulsesPerSecond >= 3) {
            element.classList.remove('emergency-pulse');
            element.classList.add('emergency-pulse-critical');
        } else {
            element.classList.remove('emergency-pulse-critical');
            element.classList.add('emergency-pulse');
        }

        // Set custom animation duration via inline style for non-standard rates
        if (pulsesPerSecond !== 1 && pulsesPerSecond < 3) {
            element.style.animationDuration = (1 / pulsesPerSecond) + 's';
        }
    },

    /**
     * Remove pulsing animation from an element.
     * @param {HTMLElement} element - DOM element to stop animating
     */
    removePulseAnimation(element) {
        if (!element) return;
        element.classList.remove('emergency-pulse', 'emergency-pulse-critical');
        element.style.animationDuration = '';
    },

    // =========================================================================
    // POLLING MANAGER
    // =========================================================================

    /**
     * Start polling an API endpoint at a given interval.
     * @param {string} endpoint - API endpoint path (e.g., '/admin/emergency-orders')
     * @param {number} intervalMs - Polling interval in milliseconds
     * @param {Function} callback - Callback function receiving response data
     * @returns {number} Poll ID for stopping later
     */
    startPolling(endpoint, intervalMs, callback) {
        const pollId = ++this._pollIdCounter;

        // Immediate first fetch
        this._fetchEndpoint(endpoint, callback);

        // Set up interval
        this._pollingIntervals[pollId] = setInterval(() => {
            this._fetchEndpoint(endpoint, callback);
        }, intervalMs);

        return pollId;
    },

    /**
     * Stop a polling interval by its ID.
     * @param {number} pollId - The poll ID returned by startPolling
     */
    stopPolling(pollId) {
        if (this._pollingIntervals[pollId]) {
            clearInterval(this._pollingIntervals[pollId]);
            delete this._pollingIntervals[pollId];
        }
    },

    /**
     * Fetch an endpoint and pass data to callback.
     * @private
     */
    async _fetchEndpoint(endpoint, callback) {
        try {
            if (typeof api !== 'undefined') {
                const data = await api.get(endpoint);
                callback(data, null);
            } else {
                // Fallback if api.js not loaded
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const headers = { 'Accept': 'application/json' };
                if (token) headers['Authorization'] = 'Bearer ' + token;

                const response = await fetch(endpoint, { headers });
                const data = await response.json();
                callback(data, null);
            }
        } catch (error) {
            callback(null, error);
        }
    },

    // =========================================================================
    // SLA COUNTDOWN & FORMATTING
    // =========================================================================

    /**
     * Format remaining seconds as HH:MM:SS countdown string.
     * @param {number} remainingSeconds - Seconds remaining
     * @returns {string} Formatted time string (e.g., "00:35:12")
     */
    formatCountdown(remainingSeconds) {
        if (remainingSeconds < 0) remainingSeconds = 0;

        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = Math.floor(remainingSeconds % 60);

        return String(hours).padStart(2, '0') + ':' +
               String(minutes).padStart(2, '0') + ':' +
               String(seconds).padStart(2, '0');
    },

    /**
     * Format elapsed breach time as MM:SS string.
     * @param {number} elapsedSeconds - Seconds elapsed past SLA deadline
     * @returns {string} Formatted breach time (e.g., "05:32")
     */
    formatElapsedBreach(elapsedSeconds) {
        if (elapsedSeconds < 0) elapsedSeconds = 0;

        const minutes = Math.floor(elapsedSeconds / 60);
        const seconds = Math.floor(elapsedSeconds % 60);

        return String(minutes).padStart(2, '0') + ':' +
               String(seconds).padStart(2, '0');
    },

    /**
     * Calculate SLA progress percentage (0-100) based on order creation time.
     * @param {string|Date} createdAt - Order creation timestamp
     * @param {number} slaMinutes - SLA window in minutes (default: 40)
     * @returns {number} Progress percentage (0-100, capped at 100)
     */
    calculateSLAProgress(createdAt, slaMinutes = 40) {
        const createdTime = new Date(createdAt).getTime();
        const now = Date.now();
        const elapsedMs = now - createdTime;
        const totalMs = slaMinutes * 60 * 1000;

        if (elapsedMs <= 0) return 0;

        const progress = (elapsedMs / totalMs) * 100;
        return Math.min(100, Math.round(progress * 10) / 10);
    }
};
