import { readFileSync } from 'node:fs';
import { expect, test } from '@playwright/test';

const admin = () => JSON.parse(readFileSync('tests/browser/.auth/admin.json', 'utf8'));

test.describe.configure({ mode: 'serial' });

test.use({ viewport: { width: 1280, height: 900 } });

test('contact form shows validation errors, then succeeds', async ({ page }) => {
    await page.goto('/contact');

    await page.locator('#field-name').fill('زائر الاختبار');
    await page.locator('#field-email').fill('not-an-email');
    await page.waitForTimeout(3500); // the form must be open for a few seconds
    await page.locator('[data-submit]').click();

    const alert = page.locator('.alert--error');
    await expect(alert).toBeVisible();
    await expect(alert).toBeFocused();
    await expect(page.locator('#field-name')).toHaveValue('زائر الاختبار');
    await expect(page.locator('#field-email')).toHaveAttribute('aria-invalid', 'true');

    await page.locator('#field-email').fill('qa-visitor@example.test');
    await page.locator('#field-organization').fill('جهة الاختبار');
    await page.locator('#field-inquiry_type').selectOption('quote');
    await page.locator('#field-message').fill('رسالة اختبار آلي للتحقق من عمل نموذج التواصل.');
    await expect(page.locator('[data-char-count-value]')).toHaveText(String('رسالة اختبار آلي للتحقق من عمل نموذج التواصل.'.length));
    await page.locator('#field-consent').check();
    await page.waitForTimeout(3500);
    await page.locator('[data-submit]').click();

    await expect(page.locator('.alert--success')).toBeVisible();
    await expect(page.locator('.alert--success')).toBeFocused();
});

test('the message appears in the admin inbox; logout blocks access', async ({ page }) => {
    const { email, password } = admin();

    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login$/);

    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.getByRole('button', { name: 'دخول' }).click();
    await expect(page).toHaveURL(/\/admin$/);
    await expect(page.locator('h1')).toHaveText('لوحة المتابعة');

    await page.goto('/admin/messages');
    await page.getByRole('row', { name: /زائر الاختبار/ }).getByRole('link').click();
    await expect(page.locator('.message-body')).toContainText('رسالة اختبار آلي');

    await page.getByRole('button', { name: 'أرشفة' }).click();
    await expect(page.locator('.notice--success')).toBeVisible();

    await page.getByRole('button', { name: 'تسجيل الخروج' }).click();
    await expect(page).toHaveURL(/\/admin\/login$/);
    await page.goto('/admin/messages');
    await expect(page).toHaveURL(/\/admin\/login$/);
});

test('admin rejects SVG uploads', async ({ page }) => {
    const { email, password } = admin();

    await page.goto('/admin/login');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.getByRole('button', { name: 'دخول' }).click();

    await page.goto('/admin/slides/create');
    await page.locator('#f-heading_ar').fill('اختبار');
    await page.locator('#f-image').setInputFiles({
        name: 'evil.svg',
        mimeType: 'image/svg+xml',
        buffer: Buffer.from('<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
    });
    await page.getByRole('button', { name: 'حفظ' }).click();
    await expect(page.locator('.notice--error')).toBeVisible();
    await expect(page.locator('.a-field--invalid:has(#f-image) .a-field__error')).toBeVisible();
    await expect(page.locator('.a-field--invalid:has(#f-image) .a-field__error')).toContainText('jpg');
});
