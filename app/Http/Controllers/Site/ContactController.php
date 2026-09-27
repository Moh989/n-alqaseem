<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\PageSection;
use App\Rules\Turnstile;
use App\Services\Contact\SpamGuard;
use App\Services\Seo\SeoManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

use function Illuminate\Support\defer;

class ContactController extends Controller
{
    public function show(Request $request, SeoManager $seo, SpamGuard $guard): View
    {
        $seo->forPage('contact');

        $type = $request->query('type');

        return view('site.contact', [
            'intro' => PageSection::where('page', 'contact')->where('key', 'intro')->where('is_published', true)->first(),
            'formToken' => $guard->token(),
            'inquiryTypes' => ContactRequest::INQUIRY_TYPES,
            'selectedType' => in_array($type, ContactRequest::INQUIRY_TYPES, true) ? $type : null,
            'turnstileKey' => Turnstile::enabled() ? config('site.turnstile.site_key') : null,
        ]);
    }

    public function store(ContactRequest $request, SpamGuard $guard): RedirectResponse
    {
        $back = redirect()->to(lroute('contact').'#contact-form');

        // Bots get the normal success message so the honeypot is not revealed.
        if ($guard->isHoneypotFilled($request)) {
            return $back->with('contact_success', __('contact.success'));
        }

        $timestamp = $guard->checkTimestamp($request);

        if ($timestamp !== 'ok') {
            return $back->withInput()->with('contact_error', __('contact.errors.'.($timestamp === 'expired' ? 'expired' : 'token')));
        }

        $message = ContactMessage::create([
            ...$request->safe()->only(['name', 'organization', 'email', 'inquiry_type', 'message']),
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        $this->notify($message);

        return $back->with('contact_success', __('contact.success'));
    }

    /**
     * Email the notification address after the response is sent; failures are logged, never shown.
     */
    protected function notify(ContactMessage $message): void
    {
        $recipient = site('notify_email');

        if (! config('site.contact.notify') || blank($recipient)) {
            return;
        }

        defer(function () use ($message, $recipient): void {
            try {
                Mail::to($recipient)->send(new ContactMessageReceived($message));
            } catch (Throwable $exception) {
                Log::warning('Contact notification email failed.', ['message_id' => $message->id, 'error' => $exception->getMessage()]);
            }
        });
    }
}
