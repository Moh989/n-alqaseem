<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Services\Contact\SpamGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSite();
        $this->withoutDefer();
        RateLimiter::clear('contact-minute:127.0.0.1');
        RateLimiter::clear('contact-day:127.0.0.1');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function payload(array $overrides = []): array
    {
        return [
            'name' => 'أحمد علي',
            'organization' => 'شركة الاختبار',
            'email' => 'Ahmed@Example.com',
            'inquiry_type' => 'quote',
            'message' => 'نرغب بالحصول على عرض لأعمال طرق في بغداد.',
            'consent' => '1',
            SpamGuard::TIMESTAMP_FIELD => Crypt::encryptString((string) now()->subSeconds(20)->getTimestamp()),
            ...$overrides,
        ];
    }

    public function test_contact_page_renders_the_form(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('name="form_token"', false)
            ->assertSee('name="website"', false)
            ->assertSee('نوع الاستفسار')
            ->assertDontSee('name="phone"', false);
    }

    public function test_valid_message_is_stored_with_a_normalised_email(): void
    {
        $this->post('/contact', $this->payload())
            ->assertRedirect(url('/contact').'#contact-form')
            ->assertSessionHas('contact_success');

        $message = ContactMessage::sole();
        $this->assertSame('ahmed@example.com', $message->email);
        $this->assertSame('ar', $message->locale);
        $this->assertSame('127.0.0.1', $message->ip_address);
    }

    public function test_english_submission_redirects_back_to_the_english_form(): void
    {
        $this->post('/en/contact', $this->payload())
            ->assertRedirect(url('/en/contact').'#contact-form');

        $this->assertSame('en', ContactMessage::sole()->locale);
    }

    public function test_validation_errors_keep_old_input(): void
    {
        $this->from('/contact')
            ->post('/contact', $this->payload(['email' => 'not-an-email', 'message' => 'short', 'consent' => null]))
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['email', 'message', 'consent'])
            ->assertSessionHasInput('name', 'أحمد علي');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_validation_messages_are_translated(): void
    {
        $this->from('/en/contact')->post('/en/contact', $this->payload(['name' => '']))
            ->assertSessionHasErrors(['name' => 'The Name field is required.']);

        $this->from('/contact')->post('/contact', $this->payload(['name' => '']))
            ->assertSessionHasErrors(['name' => 'حقل الاسم مطلوب.']);
    }

    public function test_honeypot_submissions_look_successful_but_are_discarded(): void
    {
        $this->post('/contact', $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHas('contact_success');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_too_fast_or_tampered_submissions_are_rejected(): void
    {
        $this->post('/contact', $this->payload([SpamGuard::TIMESTAMP_FIELD => Crypt::encryptString((string) now()->getTimestamp())]))
            ->assertSessionHas('contact_error');

        $this->post('/contact', $this->payload([SpamGuard::TIMESTAMP_FIELD => 'tampered']))
            ->assertSessionHas('contact_error');

        $this->post('/contact', $this->payload([SpamGuard::TIMESTAMP_FIELD => Crypt::encryptString((string) now()->subHours(3)->getTimestamp())]))
            ->assertSessionHas('contact_error');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_submissions_are_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/contact', $this->payload())->assertSessionHas('contact_success');
        }

        $this->from('/contact')->post('/contact', $this->payload())
            ->assertRedirect('/contact')
            ->assertSessionHas('contact_error', __('contact.throttled', [], 'ar'));

        $this->assertDatabaseCount('contact_messages', 3);
    }

    public function test_notification_email_is_sent_only_when_enabled(): void
    {
        Mail::fake();

        $this->post('/contact', $this->payload());
        Mail::assertNothingSent();

        config(['site.contact.notify' => true]);
        $this->post('/contact', $this->payload());

        Mail::assertSent(ContactMessageReceived::class, fn (ContactMessageReceived $mail): bool => $mail->hasTo('info@n-alqaseem.com')
            && $mail->hasReplyTo('ahmed@example.com'));
    }

    public function test_turnstile_is_verified_when_configured(): void
    {
        config(['site.turnstile.site_key' => 'site', 'site.turnstile.secret_key' => 'secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::sequence()->push(['success' => false])->push(['success' => true])]);

        $this->get('/contact')->assertSee('cf-turnstile', false);

        $this->post('/contact', $this->payload(['cf-turnstile-response' => 'bad']))->assertSessionHasErrors('cf-turnstile-response');
        $this->post('/contact', $this->payload(['cf-turnstile-response' => 'good']))->assertSessionHas('contact_success');

        $this->assertDatabaseCount('contact_messages', 1);
    }
}
