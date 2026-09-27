/** Progressive enhancements for the contact form (it works fully without JavaScript). */
export function initContactForm() {
    // Move focus to the result message; repeat after load because jumping to the
    // #contact-form fragment can reset focus once the page has finished loading.
    const alert = document.querySelector('[data-focus-on-load]');

    if (alert) {
        alert.focus();
        window.addEventListener('load', () => requestAnimationFrame(() => alert.focus()), { once: true });
    }

    const form = document.querySelector('[data-contact-form]');

    if (!form) {
        return;
    }

    const textarea = form.querySelector('[data-char-count]');
    const counter = form.querySelector('[data-char-count-value]');

    if (textarea && counter) {
        const update = () => (counter.textContent = String(textarea.value.length));
        textarea.addEventListener('input', update);
        update();
    }

    let submitting = false;

    form.addEventListener('submit', (event) => {
        if (submitting) {
            event.preventDefault();

            return;
        }

        submitting = true;
        const button = form.querySelector('[data-submit]');

        if (button) {
            button.setAttribute('aria-disabled', 'true');
            button.firstChild.textContent = `${button.dataset.sendingLabel} `;
        }
    });
}
