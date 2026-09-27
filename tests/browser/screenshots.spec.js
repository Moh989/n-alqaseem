import { readFileSync } from 'node:fs';
import { test } from '@playwright/test';
import { loadLazyImages } from './helpers.js';

/** Captures the screenshots referenced in docs/QA.md. */
const shots = [
    ['/', 1440, 'home-ar-1440'],
    ['/', 390, 'home-ar-390'],
    ['/en', 1440, 'home-en-1440'],
    ['/about', 1440, 'about-ar-1440'],
    ['/en/services', 1440, 'services-en-1440'],
    ['/services/infrastructure-roads', 1440, 'service-ar-1440'],
    ['/contact', 390, 'contact-ar-390'],
    ['/en/contact', 1440, 'contact-en-1440'],
];

test.use({ reducedMotion: 'reduce' });

for (const [path, width, name] of shots) {
    test(`screenshot ${name}`, async ({ page }) => {
        await page.setViewportSize({ width, height: width < 600 ? 844 : 900 });
        await page.goto(path, { waitUntil: 'networkidle' });
        await loadLazyImages(page);
        await page.screenshot({ path: `docs/screenshots/${name}.png`, fullPage: true });
    });
}

test('screenshot services dropdown', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/');
    await page.locator('.has-dropdown .primary-nav__link').hover();
    await page.waitForTimeout(300);
    await page.screenshot({ path: 'docs/screenshots/dropdown-ar-1440.png' });
});

test('screenshot mobile menu', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/en');
    await page.locator('[data-offcanvas-open]').click();
    await page.waitForTimeout(400);
    await page.screenshot({ path: 'docs/screenshots/menu-en-390.png' });
});

test('screenshot admin dashboard', async ({ page }) => {
    const { email, password } = JSON.parse(readFileSync('tests/browser/.auth/admin.json', 'utf8'));
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/admin/login');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.getByRole('button', { name: 'دخول' }).click();
    await page.screenshot({ path: 'docs/screenshots/admin-dashboard.png', fullPage: true });
    await page.goto('/admin/settings');
    await page.screenshot({ path: 'docs/screenshots/admin-settings.png' });
});
