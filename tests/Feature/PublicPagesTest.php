<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSite();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        $paths = ['/', '/about', '/services', '/contact', '/privacy', '/terms'];
        $cases = [];

        foreach ($paths as $path) {
            $cases['ar '.$path] = [$path, 'ar'];
            $cases['en '.$path] = [rtrim('/en'.$path, '/'), 'en'];
        }

        return $cases;
    }

    #[DataProvider('pages')]
    public function test_public_page_renders_with_language_seo_and_one_h1(string $path, string $locale): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertSee('<html lang="'.$locale.'" dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'">', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('hreflang="ar"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="x-default"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('application/ld+json', false);
        $this->assertSame(1, $this->countMatches($response, '/<h1[\s>]/'), 'Each page must have exactly one <h1>.');
    }

    public function test_every_service_has_a_page_in_both_languages(): void
    {
        $services = Service::all();

        $this->assertCount(10, $services);

        foreach ($services as $service) {
            $this->get('/services/'.$service->slug)->assertOk()->assertSee($service->title_ar);
            $this->get('/en/services/'.$service->slug)->assertOk()->assertSee($service->title_en);
        }
    }

    public function test_language_switcher_points_to_the_same_page(): void
    {
        $this->get('/services/marine-services')
            ->assertSee('href="'.url('/en/services/marine-services').'" hreflang="en"', false);

        $this->get('/en/about')
            ->assertSee('href="'.url('/about').'" hreflang="ar"', false);
    }

    public function test_unknown_pages_return_a_localized_404(): void
    {
        $this->get('/services/does-not-exist')->assertNotFound()->assertSee('الصفحة غير موجودة');
        $this->get('/en/services/does-not-exist')->assertNotFound()->assertSee('Page not found')->assertSee('<html lang="en"', false);
        $this->get('/en/nothing-here')->assertNotFound()->assertSee('Page not found');
    }

    public function test_unpublished_services_are_hidden_everywhere(): void
    {
        Service::where('slug', 'marine-services')->update(['is_published' => false]);

        $this->get('/services/marine-services')->assertNotFound();
        $this->get('/services')->assertDontSee('/services/marine-services');
        $this->get('/')->assertDontSee('/services/marine-services');
        $this->get('/sitemap.xml')->assertDontSee('marine-services');
    }

    public function test_services_dropdown_lists_all_categories_and_services(): void
    {
        $response = $this->get('/');

        $response->assertSee('aria-controls="services-menu"', false);
        $response->assertSee('الإنشاءات والبنية التحتية');
        $response->assertSee('الطاقة والنفط');
        $response->assertSee('التجارة والخدمات المساندة');
        $this->assertSame(10, $this->countMatches($response, '/class="dropdown__link"/'));
    }

    public function test_contact_details_are_shown_with_a_maps_search_link_and_no_embedded_map(): void
    {
        $response = $this->get('/contact');

        $response->assertSee('mailto:info@n-alqaseem.com', false);
        $response->assertSee('العراق – بغداد – حي الكيلاني');
        $response->assertSee('https://www.google.com/maps/search/?api=1&amp;query=', false);
        $response->assertDontSee('<iframe', false);
    }

    public function test_company_profile_buttons_fall_back_to_the_about_page_without_a_pdf(): void
    {
        $response = $this->get('/');

        $this->assertSame(1, $this->countMatches($response, '#<a href="'.preg_quote(url('/about'), '#').'"\s+class="btn btn--primary btn--sm site-header__cta"#'));
        $this->assertGreaterThanOrEqual(4, $this->countMatches($response, '/الملف التعريفي/u'), 'Header, mobile menu, hero slide, intro and footer.');
        $response->assertDontSee('company-profile.pdf');

        $this->get('/en')->assertSee('Company profile')->assertSee('View company profile');
    }

    public function test_illustrative_image_notes_are_not_shown(): void
    {
        foreach (['/', '/services/oil-services', '/terms', '/en', '/en/services/oil-services', '/en/terms'] as $path) {
            $this->get($path)->assertDontSee('توضيحية')->assertDontSee('لا تمثل')->assertDontSee('illustrative');
        }
    }

    public function test_projects_pages_no_longer_exist(): void
    {
        $this->get('/projects')->assertNotFound();
        $this->get('/en/projects')->assertNotFound();
        $this->get('/')->assertDontSee(url('/projects'));
        $this->get('/sitemap.xml')->assertDontSee('/projects');
    }

    public function test_security_headers_are_sent(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("default-src 'self'", (string) $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'none'", (string) $response->headers->get('Content-Security-Policy'));
    }
}
