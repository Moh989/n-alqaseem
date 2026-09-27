<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards against invented claims or leftover placeholder text on the public site.
 */
class ContentGuardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var list<string>
     */
    protected const DENYLIST = [
        'ISO', 'certif', 'شهادة', 'شهادات', 'أسطول', 'fleet', 'factory', 'مصنع', 'مصافي',
        'الملف الأصلي', 'الملف التعريفي الأصلي', 'source profile', 'source material',
        'lorem', 'ipsum', 'TODO', 'placeholder', 'أحد أهم', 'رائدة', 'الرائدة', 'مبهرة', 'world-class', 'leading company',
        // Stock photos are not captioned or disclaimed on the public site.
        'توضيحية', 'لا تمثل', 'illustrative',
        // The company is reached by email only.
        'هاتف', 'phone', '785 150', '+964',
        // Project references were removed from the website.
        'مشاريعنا', 'مشاريع منجزة', 'قيد الإنجاز', 'الغالبية', 'الجادرية', 'خمسة طوابق', 'ديالى', 'Our projects', 'Ghalibiyah', 'Jadriya', 'Diyala',
    ];

    public function test_public_pages_contain_no_invented_claims_or_placeholders(): void
    {
        $this->seedSite();

        $paths = ['/', '/about', '/services', '/contact', '/privacy', '/terms'];
        $paths = [...$paths, ...array_map(fn (string $path): string => rtrim('/en'.$path, '/'), $paths)];

        foreach (Service::pluck('slug') as $slug) {
            $paths[] = '/services/'.$slug;
            $paths[] = '/en/services/'.$slug;
        }

        foreach ($paths as $path) {
            $html = (string) $this->get($path)->assertOk()->getContent();

            $this->assertStringNotContainsString('tel:', $html, "Telephone link found on {$path}");
            $this->assertStringNotContainsString('type="tel"', $html, "Phone field found on {$path}");
            $text = html_entity_decode(strip_tags(preg_replace('#<(script|style)\b.*?</\1>#s', '', $html)));

            foreach (self::DENYLIST as $term) {
                $this->assertStringNotContainsStringIgnoringCase($term, $text, "“{$term}” found on {$path}");
            }
        }
    }
}
