import { isRtl, prefersReducedMotion } from './dom.js';

const INTERVAL = 7000;
const SWIPE_THRESHOLD = 50;

/**
 * Accessible crossfade carousel (WAI-ARIA carousel pattern):
 * previous/next buttons, dots, a pause/play toggle, autoplay that pauses on hover, focus
 * and hidden tabs (never starts with reduced motion), RTL-aware swipe and arrow keys.
 */
export function initSliders() {
    document.querySelectorAll('[data-slider]').forEach((root) => new Slider(root));
}

class Slider {
    constructor(root) {
        this.root = root;
        this.slides = [...root.querySelectorAll('[data-slide]')];
        this.track = root.querySelector('[data-slider-track]');
        this.dots = [...root.querySelectorAll('[data-slider-dot]')];
        this.toggle = root.querySelector('[data-slider-toggle]');
        this.counter = root.querySelector('[data-slider-current]');
        this.index = 0;
        this.timer = null;
        this.userPaused = prefersReducedMotion();
        this.hovered = false;
        this.focused = false;

        if (this.slides.length < 2) {
            return;
        }

        root.querySelector('[data-slider-prev]')?.addEventListener('click', () => this.go(this.index - 1, true));
        root.querySelector('[data-slider-next]')?.addEventListener('click', () => this.go(this.index + 1, true));
        this.dots.forEach((dot) => dot.addEventListener('click', () => this.go(Number(dot.dataset.sliderDot), true)));
        this.toggle?.addEventListener('click', () => this.setUserPaused(!this.userPaused));

        root.addEventListener('pointerenter', (event) => {
            if (event.pointerType === 'mouse') {
                this.hovered = true;
                this.schedule();
            }
        });
        root.addEventListener('pointerleave', (event) => {
            if (event.pointerType === 'mouse') {
                this.hovered = false;
                this.schedule();
            }
        });
        root.addEventListener('focusin', () => {
            this.focused = true;
            this.schedule();
        });
        root.addEventListener('focusout', (event) => {
            if (!root.contains(event.relatedTarget)) {
                this.focused = false;
                this.schedule();
            }
        });
        document.addEventListener('visibilitychange', () => this.schedule());

        root.addEventListener('keydown', (event) => this.onKeydown(event));
        this.bindSwipe();

        this.setUserPaused(this.userPaused);
    }

    get isPlaying() {
        return !this.userPaused && !this.hovered && !this.focused && !document.hidden;
    }

    go(target, fromUser = false) {
        const count = this.slides.length;
        const next = (target + count) % count;

        if (next === this.index) {
            return;
        }

        this.slides[this.index].classList.remove('is-active');
        this.slides[this.index].inert = true;
        this.slides[next].classList.add('is-active');
        this.slides[next].inert = false;
        this.index = next;

        this.dots.forEach((dot, i) => {
            if (i === next) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });

        if (this.counter) {
            this.counter.textContent = String(next + 1).padStart(2, '0');
        }

        this.root.dispatchEvent(new CustomEvent('slider:change', { detail: { index: next } }));

        if (fromUser) {
            this.schedule();
        }
    }

    setUserPaused(paused) {
        this.userPaused = paused;
        this.root.classList.toggle('is-paused', paused);
        // Announce slide changes only while the rotation is stopped.
        this.track?.setAttribute('aria-live', paused ? 'polite' : 'off');

        if (this.toggle) {
            this.toggle.setAttribute('aria-label', paused ? this.toggle.dataset.labelPlay : this.toggle.dataset.labelPause);
        }

        this.schedule();
    }

    schedule() {
        clearTimeout(this.timer);

        if (this.isPlaying) {
            this.timer = setTimeout(() => {
                this.go(this.index + 1);
                this.schedule();
            }, INTERVAL);
        }
    }

    onKeydown(event) {
        if (!['ArrowLeft', 'ArrowRight'].includes(event.key) || event.target.matches('input, textarea, select')) {
            return;
        }

        event.preventDefault();
        // Arrow keys follow the visual direction: in RTL the "next" slide lies to the left.
        const forward = isRtl() ? event.key === 'ArrowLeft' : event.key === 'ArrowRight';
        this.go(this.index + (forward ? 1 : -1), true);
    }

    bindSwipe() {
        let start = null;

        this.track?.addEventListener('pointerdown', (event) => {
            if (event.pointerType !== 'mouse') {
                start = { x: event.clientX, y: event.clientY };
            }
        });

        this.track?.addEventListener('pointerup', (event) => {
            if (!start) {
                return;
            }

            const dx = event.clientX - start.x;
            const dy = event.clientY - start.y;
            start = null;

            if (Math.abs(dx) < SWIPE_THRESHOLD || Math.abs(dx) < Math.abs(dy)) {
                return;
            }

            // Swiping toward the reading direction's end reveals the next slide.
            const forward = isRtl() ? dx > 0 : dx < 0;
            this.go(this.index + (forward ? 1 : -1), true);
        });

        this.track?.addEventListener('pointercancel', () => (start = null));
    }
}
