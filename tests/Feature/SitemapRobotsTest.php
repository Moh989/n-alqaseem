<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapRobotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_every_page_in_both_languages_with_alternates(): void
    {
        $this->seedSite();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringStartsWith('application/xml', (string) $response->headers->get('Content-Type'));

        $xml = simplexml_load_string((string) $response->getContent());
        $this->assertNotFalse($xml, 'Sitemap must be well-formed XML.');
        $this->assertCount(32, $xml->url, '6 pages + 10 services, in 2 languages.');

        $response->assertSee('<loc>'.url('/').'</loc>', false);
        $response->assertSee('<loc>'.url('/en/services/general-trading').'</loc>', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /');
    }

    public function test_robots_allows_indexing_in_production_and_hides_admin(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }
}
