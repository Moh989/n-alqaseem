export const PAGES = ['/', '/about', '/services', '/services/general-contracting', '/contact', '/privacy'];

export const WIDTHS = [360, 390, 768, 1440];

/** Collects console errors and uncaught exceptions for a page. */
export function trackErrors(page) {
    const errors = [];
    page.on('console', (message) => {
        if (message.type() === 'error') {
            errors.push(message.text());
        }
    });
    page.on('pageerror', (error) => errors.push(error.message));

    return errors;
}

/** Scrolls through the page so lazy images load before screenshots. */
export async function loadLazyImages(page) {
    await page.evaluate(async () => {
        for (let y = 0; y < document.body.scrollHeight; y += 600) {
            window.scrollTo(0, y);
            await new Promise((resolve) => setTimeout(resolve, 60));
        }
        window.scrollTo(0, 0);
    });
    await page.waitForLoadState('networkidle');
}
