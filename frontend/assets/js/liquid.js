/**
 * MediMitra Liquid UI Integration
 * Handles interactive gooey blobs and liquid animations
 */

class LiquidUI {
    constructor() {
        this.blobs = [];
        this.container = document.body;
        this.init();
    }

    init() {
        // Create SVG filter if it doesn't exist
        if (!document.getElementById('ghost-filter')) {
            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.id = "ghost-filter";
            svg.style.display = "none";
            svg.innerHTML = `
                <defs>
                    <filter id="goo">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="12" result="blur" />
                        <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 25 -12" result="goo" />
                        <feComposite in="SourceGraphic" in2="goo" operator="atop" />
                    </filter>
                </defs>
            `;
            document.body.appendChild(svg);
        }

        // Create Liquid Canvas if not exists
        if (!document.querySelector('.liquid-canvas')) {
            const canvas = document.createElement('div');
            canvas.className = 'liquid-canvas';
            canvas.innerHTML = `
                <div class="liquid-orb orb-1"></div>
                <div class="liquid-orb orb-2"></div>
                <div class="liquid-orb orb-3"></div>
            `;
            document.body.prepend(canvas);
        }

        // Create Gooey Layer
        const gooeyLayer = document.createElement('div');
        gooeyLayer.className = 'gooey-layer';
        this.container.appendChild(gooeyLayer);

        // Create Blobs
        for(let i=0; i<3; i++) {
            const blob = document.createElement('div');
            blob.className = 'goo-blob';
            const size = 150 + Math.random() * 50;
            blob.style.width = `${size}px`;
            blob.style.height = `${size}px`;
            blob.style.background = `hsla(${210 + i*20}, 100%, 50%, 0.15)`;
            gooeyLayer.appendChild(blob);
            
            this.blobs.push({
                el: blob,
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2
            });
        }

        this.setupEvents();
        this.animate();
    }

    // Helper to wrap existing dashboard into liquid layout
    applyToDashboard(sidebarSelector, mainSelector) {
        const sidebar = document.querySelector(sidebarSelector);
        const main = document.querySelector(mainSelector);
        
        if (!sidebar || !main) return;

        const card = document.createElement('div');
        card.className = 'squircle-card';
        
        const liquidSidebar = document.createElement('aside');
        liquidSidebar.className = 'liquid-sidebar';
        
        const liquidMain = document.createElement('main');
        liquidMain.className = 'liquid-main';

        // Move children
        while (sidebar.firstChild) liquidSidebar.appendChild(sidebar.firstChild);
        while (main.firstChild) liquidMain.appendChild(main.firstChild);

        card.appendChild(liquidSidebar);
        card.appendChild(liquidMain);

        // Clear body and add card
        const roots = [sidebar, main, document.getElementById('sidebar-root'), document.getElementById('header-root'), document.getElementById('footer-root')];
        roots.forEach(r => r && r.remove());
        
        document.body.appendChild(card);
    }

    setupEvents() {
        window.addEventListener('mousemove', (e) => {
            if (this.blobs[0]) {
                this.blobs[0].targetX = e.clientX - 75;
                this.blobs[0].targetY = e.clientY - 75;
            }
        });

        window.addEventListener('resize', () => {
            this.blobs.forEach(b => {
                b.x = Math.min(b.x, window.innerWidth);
                b.y = Math.min(b.y, window.innerHeight);
            });
        });
    }

    animate() {
        this.blobs.forEach((b, index) => {
            if (index === 0 && b.targetX !== undefined) {
                b.x += (b.targetX - b.x) * 0.05;
                b.y += (b.targetY - b.y) * 0.05;
            } else {
                b.x += b.vx;
                b.y += b.vy;

                if(b.x < -100 || b.x > window.innerWidth) b.vx *= -1;
                if(b.y < -100 || b.y > window.innerHeight) b.vy *= -1;
            }
            
            b.el.style.transform = `translate(${b.x}px, ${b.y}px)`;
        });
        requestAnimationFrame(() => this.animate());
    }
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    window.liquidUI = new LiquidUI();
});
