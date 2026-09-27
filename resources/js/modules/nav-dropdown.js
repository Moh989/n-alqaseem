/**
 * Services dropdown — WAI-ARIA "disclosure navigation" pattern.
 * Mouse hover opens it; click/tap and Enter/Space toggle it; arrow keys move between links;
 * Escape closes it and returns focus to the toggle.
 */
export function initDropdowns() {
    document.querySelectorAll('[data-dropdown]').forEach((item) => {
        const toggle = item.querySelector('[data-dropdown-toggle]');
        const panel = item.querySelector('[data-dropdown-panel]');

        if (!toggle || !panel) {
            return;
        }

        let closeTimer;
        let openedByHover = false;
        const links = () => [...panel.querySelectorAll('a')];
        const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

        const open = () => {
            clearTimeout(closeTimer);
            panel.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
        };

        const close = ({ focusToggle = false } = {}) => {
            clearTimeout(closeTimer);
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            openedByHover = false;

            if (focusToggle) {
                toggle.focus();
            }
        };

        toggle.addEventListener('click', () => {
            if (isOpen() && !openedByHover) {
                close();
            } else {
                open();
                openedByHover = false;
            }
        });

        item.addEventListener('pointerenter', (event) => {
            if (event.pointerType === 'mouse' && !isOpen()) {
                open();
                openedByHover = true;
            }
        });

        item.addEventListener('pointerleave', (event) => {
            if (event.pointerType === 'mouse' && openedByHover) {
                closeTimer = setTimeout(() => close(), 180);
            }
        });

        item.addEventListener('keydown', (event) => {
            const items = links();
            const index = items.indexOf(document.activeElement);

            switch (event.key) {
                case 'Escape':
                    if (isOpen()) {
                        event.preventDefault();
                        close({ focusToggle: true });
                    }
                    break;
                case 'ArrowDown':
                    event.preventDefault();
                    if (!isOpen()) {
                        open();
                    }
                    items[index < 0 ? 0 : Math.min(index + 1, items.length - 1)]?.focus();
                    break;
                case 'ArrowUp':
                    if (index >= 0) {
                        event.preventDefault();
                        items[Math.max(index - 1, 0)]?.focus();
                    }
                    break;
                case 'Home':
                    if (index >= 0) {
                        event.preventDefault();
                        items[0]?.focus();
                    }
                    break;
                case 'End':
                    if (index >= 0) {
                        event.preventDefault();
                        items[items.length - 1]?.focus();
                    }
                    break;
            }
        });

        item.addEventListener('focusout', (event) => {
            if (isOpen() && !item.contains(event.relatedTarget)) {
                close();
            }
        });

        document.addEventListener('pointerdown', (event) => {
            if (isOpen() && !item.contains(event.target)) {
                close();
            }
        });
    });
}
