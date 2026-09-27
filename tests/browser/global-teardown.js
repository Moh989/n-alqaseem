import { execFileSync } from 'node:child_process';
import { rmSync } from 'node:fs';

export default async function globalTeardown() {
    execFileSync('php', [
        'artisan',
        'tinker',
        '--execute',
        "$qa = App\\Models\\User::where('email', 'qa-admin@example.test')->first(); if ($qa) { App\\Models\\AuditLog::where('user_id', $qa->id)->delete(); $qa->delete(); } App\\Models\\ContactMessage::where('email', 'qa-visitor@example.test')->delete(); Illuminate\\Support\\Facades\\RateLimiter::clear('contact-minute:127.0.0.1');",
    ]);
    rmSync('tests/browser/.auth', { recursive: true, force: true });
}
