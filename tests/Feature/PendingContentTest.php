<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendingContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSite();
    }

    public function test_founding_year_is_not_published_until_approved(): void
    {
        Setting::where('key', 'founding_year')->update(['value' => '2019']);
        app(SiteSettings::class)->flush();

        $this->get('/')->assertDontSee('2019')->assertDontSee('foundingDate');

        Setting::where('key', 'founding_year')->update(['status' => Setting::STATUS_APPROVED]);
        app(SiteSettings::class)->flush();

        $this->get('/')->assertSee('سنة التأسيس')->assertSee('2019')->assertSee('"foundingDate":"2019"', false);
    }

    public function test_english_legal_name_suggestion_is_not_published_until_approved(): void
    {
        $this->get('/en')
            ->assertDontSee('Noor AlQaseem Company for General Trading and Contracting')
            ->assertSee('شركة نور القسيم للتجارة والمقاولات العامة ونقل المشتقات النفطية');
    }

    public function test_profile_pdf_button_and_download_require_an_approved_file(): void
    {
        Storage::fake('local');

        $this->get('/')->assertDontSee('/company-profile.pdf');
        $this->get('/company-profile.pdf')->assertNotFound();

        Storage::disk('local')->put('profile/ar-test.pdf', "%PDF-1.4\n%test");
        Setting::where('key', 'profile_pdf_ar')->update(['value' => 'profile/ar-test.pdf']);
        app(SiteSettings::class)->flush();

        $this->get('/company-profile.pdf')->assertNotFound();

        Setting::where('key', 'profile_pdf_ar')->update(['status' => Setting::STATUS_APPROVED]);
        app(SiteSettings::class)->flush();

        $home = $this->get('/');
        $home->assertSee('href="'.url('/company-profile.pdf').'"', false);
        $this->assertGreaterThanOrEqual(4, $this->countMatches($home, '#href="'.preg_quote(url('/company-profile.pdf'), '#').'"[^>]*target="_blank"#s'));
        $home->assertSee('type="application/pdf"', false);

        $this->get('/company-profile.pdf')->assertOk()->assertHeader('Content-Type', 'application/pdf');

        // Without an English file the English site offers the Arabic one.
        $this->get('/en')->assertSee('href="'.url('/en/company-profile.pdf').'"', false);
        $this->get('/en/company-profile.pdf')->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_first_home_slide_opens_the_profile_pdf_in_a_new_tab(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('profile/ar-test.pdf', "%PDF-1.4\n%test");
        Setting::where('key', 'profile_pdf_ar')->update(['value' => 'profile/ar-test.pdf', 'status' => Setting::STATUS_APPROVED]);
        app(SiteSettings::class)->flush();

        $this->assertSame(1, $this->countMatches(
            $this->get('/'),
            '#href="'.preg_quote(url('/company-profile.pdf'), '#').'"\s+target="_blank" rel="noopener" type="application/pdf"\s*>\s*عرض الملف التعريفي#u',
        ));
    }

    public function test_social_links_and_other_empty_settings_do_not_render(): void
    {
        $this->get('/')->assertDontSee('linkedin.com')->assertDontSee('facebook.com')->assertDontSee('"sameAs"', false);
    }
}
