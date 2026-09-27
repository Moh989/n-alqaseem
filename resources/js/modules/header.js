/** Adds a shadow to the sticky header once the page is scrolled. */
export function initHeader() {
    const header = document.querySelector('[data-header]');

    if (!header) {
        return;
    }

    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);

    update();
    window.addEventListener('scroll', update, { passive: true });
}
