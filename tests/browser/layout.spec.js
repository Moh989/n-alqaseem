import { expect, test } from '@playwright/test';
import { PAGES, WIDTHS, trackErrors } from './helpers.js';

for (const locale of ['ar', 'en']) {
    for (const width of WIDTHS) {
        test(`${locale} pages at ${width}px: no horizontal scroll, no JS errors`, async ({ page }) => {
            await page.setViewportSize({ width, height: 900 });
            const errors = trackErrors(page);

            for (const path of PAGES) {
                const url = locale === 'en' ? `/en${path === '/' ? '' : path}` : path;
                const response = await page.goto(url, { waitUntil: 'networkidle' });

                expect(response.status(), url).toBe(200);
                await expect(page.locator('html')).toHaveAttribute('dir', locale === 'ar' ? 'rtl' : 'ltr');

                const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
                expect(overflow, `${url} overflows horizontally`).toBeLessThanOrEqual(0);
            }

            expect(errors).toEqual([]);
        });
    }
}

test('language switch keeps the current page', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/services/marine-services');
    await page.locator('.site-header .lang-switch').click();
    await expect(page).toHaveURL(/\/en\/services\/marine-services$/);
    await expect(page.locator('h1')).toHaveText('Marine Services');

    await page.locator('.site-header .lang-switch').click();
    await expect(page).toHaveURL(/\/services\/marine-services$/);
    await expect(page.locator('h1')).toHaveText('الخدمات البحرية');
});

test('images are served as responsive WebP with lazy loading below the fold', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/services');

    const first = page.locator('.hero__slide').first().locator('img');
    await expect(first).toHaveAttribute('fetchpriority', 'high');
    await expect(first).toHaveAttribute('loading', 'eager');

    const cardImage = page.locator('.service-card img').first();
    await expect(cardImage).toHaveAttribute('loading', 'lazy');
    await expect(page.locator('.service-card source[type="image/webp"]').first()).toHaveAttribute('srcset', /w480\.webp 480w/);
});
