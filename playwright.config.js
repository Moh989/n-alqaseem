import { defineConfig, devices } from '@playwright/test';

const port = process.env.QA_PORT ?? '8011';

export default defineConfig({
    testDir: './tests/browser',
    timeout: 60_000,
    fullyParallel: false,
    workers: 1,
    reporter: [['list']],
    globalSetup: './tests/browser/global-setup.js',
    globalTeardown: './tests/browser/global-teardown.js',
    use: {
        baseURL: `http://127.0.0.1:${port}`,
        trace: 'retain-on-failure',
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
    webServer: {
        command: `PHP_INI_SCAN_DIR=":${process.cwd()}/.dev" php artisan serve --host=127.0.0.1 --port=${port} --no-reload`,
        url: `http://127.0.0.1:${port}/up`,
        reuseExistingServer: true,
        timeout: 60_000,
    },
});
