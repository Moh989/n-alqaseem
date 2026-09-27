<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\PageSection;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seedSite();
        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_every_admin_screen_renders(): void
    {
        $service = Service::first();
        $slide = Slide::first();
        $message = ContactMessage::create(['locale' => 'ar', 'name' => 'زائر', 'email' => 'v@example.com', 'inquiry_type' => 'general', 'message' => 'مرحباً بكم']);

        $urls = [
            route('admin.dashboard'), route('admin.settings.index'), route('admin.pages.index'), route('admin.pages.edit', 'about'),
            route('admin.pages.edit', 'privacy'), route('admin.slides.index'), route('admin.slides.create'), route('admin.slides.edit', $slide),
            route('admin.categories.index'), route('admin.categories.create'), route('admin.services.index'), route('admin.services.create'),
            route('admin.services.edit', $service),
            route('admin.seo.index'), route('admin.seo.edit', 'home'), route('admin.profile-pdf.index'), route('admin.messages.index'),
            route('admin.messages.show', $message), route('admin.account.edit'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }

        $this->assertNotNull($message->fresh()->read_at, 'Opening a message marks it as read.');
    }

    public function test_dashboard_lists_pending_items(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertSee('سنة التأسيس')
            ->assertSee('الاسم القانوني (إنجليزي)')
            ->assertSee('بحاجة إلى مراجعة قانونية')
            ->assertDontSee('admin/projects');
    }

    public function test_settings_can_be_filled_and_approved(): void
    {
        $this->put(route('admin.settings.update', 'company'), [
            'settings' => [
                'short_name' => ['value_ar' => 'نور القسيم', 'value_en' => 'Noor AlQaseem', 'approved' => '1'],
                'legal_name_ar' => ['value' => Setting::where('key', 'legal_name_ar')->value('value'), 'approved' => '1'],
                'legal_name_en' => ['value' => 'Noor AlQaseem Co. Ltd.', 'approved' => '1'],
                'company_type' => ['value_ar' => 'شركة عراقية محدودة المسؤولية', 'value_en' => 'Iraqi limited liability company', 'approved' => '1'],
                'founding_year' => ['value' => '2020', 'approved' => '1'],
            ],
        ])->assertRedirect();

        $this->assertSame(Setting::STATUS_APPROVED, Setting::where('key', 'founding_year')->value('status'));
        $this->get('/en')->assertSee('Noor AlQaseem Co. Ltd.')->assertSee('2020');
        $this->assertTrue(AuditLog::where('action', 'setting.update')->exists());
    }

    public function test_invalid_setting_values_are_rejected(): void
    {
        $this->put(route('admin.settings.update', 'company'), ['settings' => ['founding_year' => ['value' => '99']]])
            ->assertSessionHasErrors('settings.founding_year.value');

        $this->put(route('admin.settings.update', 'social'), ['settings' => ['social_linkedin' => ['value' => 'javascript:alert(1)']]])
            ->assertSessionHasErrors('settings.social_linkedin.value');
    }

    public function test_page_text_is_escaped_and_formatted(): void
    {
        $section = PageSection::where('page', 'about')->where('key', 'mission')->firstOrFail();

        $this->put(route('admin.sections.update', $section), [
            'title_ar' => 'رسالتنا',
            'title_en' => 'Our mission',
            'body_ar' => "فقرة أولى <script>alert(1)</script>\n\n- بند أول\n- بند ثانٍ",
            'body_en' => 'Mission text',
            'is_published' => '1',
        ])->assertRedirect();

        $this->get('/about')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertSee('<li>بند أول</li>', false);
    }

    public function test_publishing_a_service_requires_both_languages(): void
    {
        $service = Service::where('slug', 'general-trading')->firstOrFail();

        $this->put(route('admin.services.update', $service), [
            'service_category_id' => $service->service_category_id,
            'slug' => 'general-trading',
            'title_ar' => 'التجارة العامة',
            'title_en' => '',
            'summary_ar' => 'ملخص',
            'body_ar' => 'نص',
            'is_published' => '1',
        ])->assertSessionHasErrors(['title_en', 'summary_en', 'body_en']);

        $this->put(route('admin.services.update', $service), [
            'service_category_id' => $service->service_category_id,
            'slug' => 'general-trading',
            'title_ar' => 'التجارة العامة',
            'is_published' => '0',
        ])->assertSessionHasNoErrors();

        $this->get('/services/general-trading')->assertNotFound();
    }

    public function test_slide_can_be_created_with_an_image_and_whitelisted_buttons_only(): void
    {
        $this->post(route('admin.slides.store'), [
            'page' => 'home',
            'heading_ar' => 'شريحة جديدة',
            'heading_en' => 'New slide',
            'cta1_label_ar' => 'اضغط',
            'cta1_target' => 'javascript:alert(1)',
            'image' => UploadedFile::fake()->image('slide.jpg', 1920, 1080),
            'is_published' => '1',
        ])->assertSessionHasErrors('cta1_target');

        $this->post(route('admin.slides.store'), [
            'page' => 'home',
            'heading_ar' => 'شريحة جديدة',
            'heading_en' => 'New slide',
            'cta1_label_ar' => 'تواصل',
            'cta1_label_en' => 'Contact',
            'cta1_target' => 'contact',
            'image' => UploadedFile::fake()->image('slide.jpg', 1920, 1080),
            'is_published' => '1',
        ])->assertSessionHasNoErrors();

        $slide = Slide::where('heading_ar', 'شريحة جديدة')->sole();
        $this->assertNotNull($slide->media_id);
        $this->get('/')->assertSee('شريحة جديدة');
    }

    public function test_items_can_be_reordered(): void
    {
        $section = PageSection::where('page', 'about')->where('key', 'values')->firstOrFail();
        [$first, $second] = $section->items->take(2)->all();

        $this->post(route('admin.items.move', ['item' => $second, 'direction' => 'up']))->assertRedirect();

        $this->assertSame($second->id, $section->fresh()->items->first()->id);
        $this->assertSame($first->id, $section->fresh()->items->get(1)->id);
    }

    public function test_messages_can_be_archived_and_deleted(): void
    {
        $message = ContactMessage::create(['locale' => 'ar', 'name' => 'زائر', 'email' => 'v@example.com', 'inquiry_type' => 'general', 'message' => 'مرحباً بكم']);

        $this->post(route('admin.messages.archive', $message))->assertRedirect();
        $this->assertNotNull($message->fresh()->archived_at);

        $this->delete(route('admin.messages.destroy', $message))->assertRedirect();
        $this->assertModelMissing($message);
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $this->put(route('admin.account.update'), [
            'current_password' => 'wrong',
            'password' => 'another-password-99',
            'password_confirmation' => 'another-password-99',
        ])->assertSessionHasErrors('current_password');

        $this->put(route('admin.account.update'), [
            'current_password' => 'password',
            'password' => 'another-password-99',
            'password_confirmation' => 'another-password-99',
        ])->assertSessionHasNoErrors();
    }
}
