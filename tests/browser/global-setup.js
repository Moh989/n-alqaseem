import { execFileSync } from 'node:child_process';
import { randomBytes } from 'node:crypto';
import { mkdirSync, writeFileSync } from 'node:fs';

/** Creates a temporary QA administrator in the local database (removed in teardown). */
export default async function globalSetup() {
    const password = `qa-${randomBytes(12).toString('hex')}9`;
    const email = 'qa-admin@example.test';

    execFileSync('php', [
        'artisan',
        'tinker',
        '--execute',
        `App\\Models\\User::updateOrCreate(['email' => '${email}'], ['name' => 'QA', 'password' => '${password}', 'is_active' => true]); App\\Models\\ContactMessage::where('email', 'qa-visitor@example.test')->delete();`,
    ]);

    mkdirSync('tests/browser/.auth', { recursive: true });
    writeFileSync('tests/browser/.auth/admin.json', JSON.stringify({ email, password }));
}
