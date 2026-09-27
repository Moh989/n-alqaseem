import { expect, test } from '@playwright/test';

test.describe('services dropdown (desktop)', () => {
    test.use({ viewport: { width: 1440, height: 900 } });

    test('opens on hover and closes when the pointer leaves', async ({ page }) => {
        await page.goto('/');
        const toggle = page.locator('[data-dropdown-toggle]');
        const panel = page.locator('#services-menu');

        await page.locator('.has-dropdown .primary-nav__link').hover();
        await expect(panel).toBeVisible();
        await expect(toggle).toHaveAttribute('aria-expanded', 'true');

        await page.mouse.move(700, 700);
        await expect(panel).toBeHidden();
    });

    test('works with the keyboard', async ({ page }) => {
        await page.goto('/en');
        const toggle = page.locator('[data-dropdown-toggle]');
        const panel = page.locator('#services-menu');

        await toggle.focus();
        await page.keyboard.press('Enter');
        await expect(panel).toBeVisible();

        await page.keyboard.press('ArrowDown');
        await expect(panel.locator('a').first()).toBeFocused();
        await page.keyboard.press('ArrowDown');
        await expect(panel.locator('a').nth(1)).toBeFocused();
        await page.keyboard.press('End');
        await expect(panel.locator('a').last()).toBeFocused();

        await page.keyboard.press('Escape');
        await expect(panel).toBeHidden();
        await expect(toggle).toBeFocused();
        await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    });

    test('toggles on click and closes on outside click', async ({ page }) => {
        await page.goto('/');
        const toggle = page.locator('[data-dropdown-toggle]');
        const panel = page.locator('#services-menu');

        await toggle.dispatchEvent('click');
        await expect(panel).toBeVisible();

        await page.mouse.click(100, 800);
        await expect(panel).toBeHidden();
    });

    test('links lead to the service pages', async ({ page }) => {
        await page.goto('/');
        await page.locator('.has-dropdown .primary-nav__link').hover();
        await page.locator('#services-menu a', { hasText: 'إنتاج الأسفلت المؤكسد' }).click();
        await expect(page).toHaveURL(/\/services\/oxidized-asphalt-production$/);
        await expect(page.locator('h1')).toHaveText('إنتاج الأسفلت المؤكسد');
    });
});

test.describe('company profile button', () => {
    /** The button must open the published profile PDF (Web_profile.pdf) in a new tab. */
    async function expectOpensPdf(page, link) {
        await expect(link).toHaveAttribute('href', /\/company-profile\.pdf$/);
        await expect(link).toHaveAttribute('target', '_blank');

        const response = await page.request.get(await link.getAttribute('href'));
        expect(response.status()).toBe(200);
        expect(response.headers()['content-type']).toBe('application/pdf');
    }

    for (const width of [1120, 1440]) {
        test(`is visible in the desktop header at ${width}px and opens the PDF`, async ({ page }) => {
            await page.setViewportSize({ width, height: 900 });
            await page.goto('/');
            const button = page.locator('.site-header__cta');

            await expect(button).toBeVisible();
            await expect(button).toHaveText(/الملف التعريفي/);

            // The header must not wrap or overflow at this width.
            const header = await page.locator('.site-header__inner').boundingBox();
            expect(header.height).toBeLessThan(90);

            await expectOpensPdf(page, button);

            // Clicking opens a new tab (headless Chromium has no PDF viewer to render it).
            const [popup] = await Promise.all([page.waitForEvent('popup'), button.click()]);
            expect(popup).toBeTruthy();
            await popup.close();
        });
    }

    test('is offered in the first home slide, the intro, the footer and the mobile menu', async ({ page }) => {
        await page.setViewportSize({ width: 390, height: 844 });
        await page.goto('/en');

        await expectOpensPdf(page, page.locator('.hero__slide.is-active .hero__actions a', { hasText: 'View company profile' }));
        await expectOpensPdf(page, page.locator('.intro__actions a', { hasText: 'View company profile' }));
        await expectOpensPdf(page, page.locator('.site-footer a', { hasText: 'Company profile' }));

        await page.locator('[data-offcanvas-open]').click();
        const menuButton = page.locator('.offcanvas__foot a', { hasText: 'View company profile' });
        await expect(menuButton).toBeVisible();
        await expectOpensPdf(page, menuButton);
    });
});

test.describe('mobile off-canvas menu', () => {
    test.use({ viewport: { width: 390, height: 844 }, hasTouch: true, isMobile: true });

    test('traps focus, closes with Escape and returns focus', async ({ page }) => {
        await page.goto('/');
        const opener = page.locator('[data-offcanvas-open]');
        const panel = page.locator('.offcanvas__panel');

        await expect(page.locator('.primary-nav')).toBeHidden();
        await opener.tap();
        await expect(panel).toBeVisible();
        await expect(opener).toHaveAttribute('aria-expanded', 'true');
        await expect(page.locator('main')).toHaveJSProperty('inert', true);

        for (let i = 0; i < 25; i++) {
            await page.keyboard.press('Tab');
            expect(await page.evaluate(() => document.activeElement.closest('.offcanvas') !== null)).toBe(true);
        }

        await page.keyboard.press('Escape');
        await expect(panel).toBeHidden();
        await expect(opener).toBeFocused();
        await expect(page.locator('main')).toHaveJSProperty('inert', false);
    });

    test('services disclosure expands and navigates', async ({ page }) => {
        await page.goto('/en');
        await page.locator('[data-offcanvas-open]').tap();
        const toggle = page.locator('[data-disclosure-toggle]');

        await expect(page.locator('#mobile-services')).toBeHidden();
        await toggle.tap();
        await expect(toggle).toHaveAttribute('aria-expanded', 'true');
        await page.locator('#mobile-services a', { hasText: 'Oil Services' }).tap();
        await expect(page).toHaveURL(/\/en\/services\/oil-services$/);
    });
});
