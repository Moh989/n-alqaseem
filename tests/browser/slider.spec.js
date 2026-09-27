import { expect, test } from '@playwright/test';

const activeIndex = (page) => page.locator('[data-slide]').evaluateAll((slides) => slides.findIndex((s) => s.classList.contains('is-active')));
const mediaShift = (page) => page.locator('.hero__slide.is-active .hero__media').evaluate((el) => el.style.getPropertyValue('--px'));

test.describe('hero slider', () => {
    test.use({ viewport: { width: 1440, height: 900 } });

    test('buttons, dots and counter work; inactive slides are inert', async ({ page }) => {
        await page.goto('/');
        await expect.poll(() => activeIndex(page)).toBe(0);

        await page.locator('[data-slider-next]').click();
        await expect.poll(() => activeIndex(page)).toBe(1);
        await expect(page.locator('[data-slider-current]')).toHaveText('02');
        await expect(page.locator('[data-slider-dot="1"]')).toHaveAttribute('aria-current', 'true');
        await expect(page.locator('[data-slide]').first()).toHaveJSProperty('inert', true);

        await page.locator('[data-slider-prev]').click();
        await expect.poll(() => activeIndex(page)).toBe(0);

        await page.locator('[data-slider-prev]').click();
        await expect.poll(() => activeIndex(page)).toBe(3);

        await page.locator('[data-slider-dot="2"]').click();
        await expect.poll(() => activeIndex(page)).toBe(2);
    });

    test('arrow keys follow the reading direction', async ({ page }) => {
        await page.goto('/');
        await page.locator('[data-slider-toggle]').focus();
        await page.keyboard.press('ArrowLeft'); // RTL: left = next
        await expect.poll(() => activeIndex(page)).toBe(1);

        await page.goto('/en');
        await page.locator('[data-slider-toggle]').focus();
        await page.keyboard.press('ArrowRight'); // LTR: right = next
        await expect.poll(() => activeIndex(page)).toBe(1);
        await page.keyboard.press('ArrowLeft');
        await expect.poll(() => activeIndex(page)).toBe(0);
    });

    test('autoplay advances, and the toggle pauses it', async ({ page }) => {
        await page.goto('/about');
        await page.mouse.move(5, 5);
        await expect(page.locator('[data-slider]')).not.toHaveClass(/is-paused/);
        await expect.poll(() => activeIndex(page), { timeout: 10_000 }).toBe(1);

        await page.locator('[data-slider-toggle]').click();
        await expect(page.locator('[data-slider]')).toHaveClass(/is-paused/);
        await expect(page.locator('[data-slider-toggle]')).toHaveAttribute('aria-label', 'تشغيل العرض التلقائي');
        await expect(page.locator('[data-slider-track]')).toHaveAttribute('aria-live', 'polite');
    });

    test('pointer parallax moves the layers on desktop', async ({ page }) => {
        await page.goto('/');
        await page.mouse.move(200, 300);
        await page.mouse.move(1300, 700, { steps: 10 });
        await expect.poll(() => mediaShift(page)).not.toBe('');
    });
});

test.describe('reduced motion', () => {
    test.use({ viewport: { width: 1440, height: 900 }, reducedMotion: 'reduce' });

    test('autoplay and parallax are disabled', async ({ page }) => {
        await page.goto('/');
        await expect(page.locator('[data-slider]')).toHaveClass(/is-paused/);
        await page.mouse.move(200, 300);
        await page.mouse.move(1300, 700, { steps: 10 });
        await page.waitForTimeout(300);
        expect(await mediaShift(page)).toBe('');
    });
});

test.describe('touch devices', () => {
    test.use({ viewport: { width: 390, height: 844 }, hasTouch: true, isMobile: true });

    test('swipe changes slides in the RTL direction and parallax is off', async ({ page }) => {
        await page.goto('/');
        const track = page.locator('[data-slider-track]');
        const box = await track.boundingBox();
        const y = box.y + box.height / 2;

        const swipe = async (fromX, toX) => {
            await track.dispatchEvent('pointerdown', { pointerType: 'touch', clientX: fromX, clientY: y, isPrimary: true });
            await track.dispatchEvent('pointerup', { pointerType: 'touch', clientX: toX, clientY: y, isPrimary: true });
        };

        await swipe(100, 300); // RTL: swiping right reveals the next slide
        await expect.poll(() => activeIndex(page)).toBe(1);
        await swipe(300, 100);
        await expect.poll(() => activeIndex(page)).toBe(0);

        expect(await mediaShift(page)).toBe('');
    });
});
