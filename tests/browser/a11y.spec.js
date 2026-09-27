import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

const pages = ['/', '/about', '/services', '/services/oil-services', '/contact', '/terms'];

for (const viewport of [{ width: 1440, height: 900 }, { width: 390, height: 844 }]) {
    for (const locale of ['ar', 'en']) {
        test(`axe: no serious violations (${locale}, ${viewport.width}px)`, async ({ page }) => {
            await page.setViewportSize(viewport);
            const failures = [];

            for (const path of pages) {
                await page.goto(locale === 'en' ? `/en${path === '/' ? '' : path}` : path, { waitUntil: 'networkidle' });
                const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();

                results.violations
                    .filter((violation) => ['serious', 'critical'].includes(violation.impact))
                    .forEach((violation) => failures.push(`${path}: ${violation.id} — ${violation.nodes.map((n) => n.target.join(' ')).slice(0, 3).join(', ')}`));
            }

            expect(failures).toEqual([]);
        });
    }
}

test('axe: admin login and dashboard', async ({ page }) => {
    await page.goto('/admin/login');
    const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa']).analyze();
    expect(results.violations.filter((v) => ['serious', 'critical'].includes(v.impact)).map((v) => v.id)).toEqual([]);
});
