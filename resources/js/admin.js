/** Small enhancements for the admin panel; every screen also works without JavaScript. */

// Confirm destructive actions.
document.addEventListener('submit', (event) => {
    const message = event.target.dataset?.confirm;

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

// Character counters for fields with a maxlength.
document.querySelectorAll('[data-counter]').forEach((field) => {
    const counter = document.createElement('p');
    counter.className = 'counter';
    counter.setAttribute('aria-live', 'polite');
    field.insertAdjacentElement('afterend', counter);

    const update = () => (counter.textContent = `${field.value.length} / ${field.maxLength}`);
    field.addEventListener('input', update);
    update();
});

// Local preview of a selected image before upload.
document.querySelectorAll('[data-image-input]').forEach((input) => {
    const preview = input.closest('.image-field')?.querySelector('[data-image-preview]');

    input.addEventListener('change', () => {
        const file = input.files?.[0];

        if (!preview || !file || !file.type.startsWith('image/')) {
            return;
        }

        const img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(file);
        preview.replaceChildren(img);
    });
});

// Mobile sidebar.
const menuButton = document.querySelector('[data-admin-menu]');
const sidebar = document.querySelector('[data-admin-sidebar]');

if (menuButton && sidebar) {
    let backdrop = null;

    const close = () => {
        sidebar.classList.remove('is-open');
        menuButton.setAttribute('aria-expanded', 'false');
        backdrop?.remove();
        backdrop = null;
    };

    menuButton.addEventListener('click', () => {
        if (sidebar.classList.contains('is-open')) {
            close();

            return;
        }

        sidebar.classList.add('is-open');
        menuButton.setAttribute('aria-expanded', 'true');
        backdrop = document.createElement('div');
        backdrop.className = 'admin-backdrop';
        backdrop.addEventListener('click', close);
        document.body.append(backdrop);
        sidebar.querySelector('a')?.focus();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar.classList.contains('is-open')) {
            close();
            menuButton.focus();
        }
    });
}
