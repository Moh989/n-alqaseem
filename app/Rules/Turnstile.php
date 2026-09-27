<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Verifies a Cloudflare Turnstile response token (only used when keys are configured).
 */
class Turnstile implements ValidationRule
{
    public function __construct(protected ?string $ip = null) {}

    public static function enabled(): bool
    {
        return filled(config('site.turnstile.site_key')) && filled(config('site.turnstile.secret_key'));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::asForm()->timeout(8)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('site.turnstile.secret_key'),
                'response' => (string) $value,
                'remoteip' => $this->ip,
            ]);

            if (! $response->json('success')) {
                $fail(__('contact.errors.captcha'));
            }
        } catch (Throwable) {
            $fail(__('contact.errors.captcha'));
        }
    }
}
