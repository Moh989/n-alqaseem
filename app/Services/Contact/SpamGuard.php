<?php

namespace App\Services\Contact;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/**
 * Lightweight bot protection for the public contact form:
 * a hidden honeypot field and an encrypted "form rendered at" timestamp.
 */
class SpamGuard
{
    public const HONEYPOT_FIELD = 'website';

    public const TIMESTAMP_FIELD = 'form_token';

    public function token(): string
    {
        return Crypt::encryptString((string) now()->getTimestamp());
    }

    /**
     * A filled honeypot means an automated submission.
     */
    public function isHoneypotFilled(Request $request): bool
    {
        return filled($request->input(self::HONEYPOT_FIELD));
    }

    /**
     * Check the rendered-at token: it must decrypt, be old enough to rule out bots,
     * and young enough to reject replays of stale forms.
     *
     * @return 'ok'|'invalid'|'too_fast'|'expired'
     */
    public function checkTimestamp(Request $request): string
    {
        try {
            $renderedAt = (int) Crypt::decryptString((string) $request->input(self::TIMESTAMP_FIELD));
        } catch (DecryptException) {
            return 'invalid';
        }

        $elapsed = now()->getTimestamp() - $renderedAt;

        if ($elapsed < config('site.contact.min_seconds')) {
            return 'too_fast';
        }

        if ($elapsed > config('site.contact.max_age_seconds')) {
            return 'expired';
        }

        return 'ok';
    }
}
