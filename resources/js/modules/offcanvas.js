import { focusableWithin, prefersReducedMotion } from './dom.js';

/**
 * Mobile off-canvas menu: modal dialog behaviour with focus trap, Escape to close,
 * background made inert and scroll locked, focus returned to the opener.
 */
export function initOffcanvas() {
    const root = document.querySelector('[data-offcanvas]');
    const opener = document.querySelector('[data-offcanvas-open]');

    if (!root || !opener) {
        return;
    }

    const panel = root.querySelector('[role="dialog"]');
    const background = [document.querySelector('.site-header'), document.querySelector('main'), document.querySelector('.site-footer')].filter(Boolean);

    const onKeydown = (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            close();

            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const items = focusableWithin(panel);
        const first = items[0];
        const last = items[items.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    };

    function open() {
        root.hidden = false;
        opener.setAttribute('aria-expanded', 'true');
        document.body.classList.add('has-offcanvas');
        background.forEach((el) => (el.inert = true));
        document.addEventListener('keydown', onKeydown);
        requestAnimationFrame(() => {
            root.classList.add('is-open');
            (focusableWithin(panel)[0] ?? panel).focus();
        });
    }

    function close() {
        root.classList.remove('is-open');
        opener.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('has-offcanvas');
        background.forEach((el) => (el.inert = false));
        document.removeEventListener('keydown', onKeydown);
        setTimeout(() => (root.hidden = true), prefersReducedMotion() ? 0 : 320);
        opener.focus();
    }

    opener.addEventListener('click', open);
    root.querySelectorAll('[data-offcanvas-close]').forEach((button) => button.addEventListener('click', close));

    // Nested disclosure for the services list.
    root.querySelectorAll('[data-disclosure-toggle]').forEach((toggle) => {
        const target = document.getElementById(toggle.getAttribute('aria-controls'));

        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            target.hidden = expanded;
        });
    });

    // Close when the viewport grows to desktop width.
    window.matchMedia('(min-width: 1120px)').addEventListener('change', (event) => {
        if (event.matches && !root.hidden) {
            close();
        }
    });
}
