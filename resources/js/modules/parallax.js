import { prefersReducedMotion } from './dom.js';

const FINE_POINTER = '(hover: hover) and (pointer: fine)';

/**
 * Subtle layered parallax that follows the mouse on desktop only.
 * Layers declare their maximum shift in pixels with data-depth (negative = opposite direction).
 * Disabled for touch devices and when reduced motion is requested; transforms only.
 */
export function initParallax() {
    const roots = [...document.querySelectorAll('[data-parallax]')];

    if (!roots.length) {
        return;
    }

    const fine = window.matchMedia(FINE_POINTER);
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const controllers = roots.map((root) => new Parallax(root));

    const sync = () => {
        const enabled = fine.matches && !prefersReducedMotion();
        controllers.forEach((controller) => controller.setEnabled(enabled));
    };

    fine.addEventListener('change', sync);
    reduced.addEventListener('change', sync);
    sync();
}

class Parallax {
    constructor(root) {
        this.root = root;
        this.enabled = false;
        this.visible = true;
        this.target = { x: 0, y: 0 };
        this.current = { x: 0, y: 0 };
        this.frame = null;

        this.onMove = (event) => {
            if (!this.enabled || !this.visible || event.pointerType !== 'mouse') {
                return;
            }

            const rect = this.root.getBoundingClientRect();
            this.target.x = (event.clientX - rect.left) / rect.width - 0.5;
            this.target.y = (event.clientY - rect.top) / rect.height - 0.5;
            this.start();
        };

        this.onLeave = () => {
            this.target.x = 0;
            this.target.y = 0;
            this.start();
        };

        root.addEventListener('pointermove', this.onMove);
        root.addEventListener('pointerleave', this.onLeave);

        new IntersectionObserver(([entry]) => {
            this.visible = entry.isIntersecting;
        }).observe(root);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                cancelAnimationFrame(this.frame);
                this.frame = null;
            }
        });
    }

    setEnabled(enabled) {
        this.enabled = enabled;

        if (!enabled) {
            cancelAnimationFrame(this.frame);
            this.frame = null;
            this.target = { x: 0, y: 0 };
            this.current = { x: 0, y: 0 };
            this.root.querySelectorAll('[data-depth]').forEach((layer) => {
                layer.style.removeProperty('--px');
                layer.style.removeProperty('--py');
            });
        }
    }

    start() {
        if (this.frame === null && this.enabled) {
            this.frame = requestAnimationFrame(() => this.tick());
        }
    }

    tick() {
        this.current.x += (this.target.x - this.current.x) * 0.08;
        this.current.y += (this.target.y - this.current.y) * 0.08;
        this.apply();

        const settled = Math.abs(this.target.x - this.current.x) < 0.001 && Math.abs(this.target.y - this.current.y) < 0.001;
        this.frame = settled || document.hidden ? null : requestAnimationFrame(() => this.tick());
    }

    apply() {
        this.root.querySelectorAll('[data-depth]').forEach((layer) => {
            const depth = Number(layer.dataset.depth) || 0;
            layer.style.setProperty('--px', `${(-this.current.x * depth).toFixed(2)}px`);
            layer.style.setProperty('--py', `${(-this.current.y * depth).toFixed(2)}px`);
        });
    }
}
