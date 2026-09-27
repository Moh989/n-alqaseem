<?php

namespace App\Services\Seo;

use App\Support\SiteSettings;
use Illuminate\Support\HtmlString;

/**
 * Structured data for the Organization. Only approved settings are included.
 */
class JsonLd
{
    public function __construct(protected SiteSettings $settings) {}

    /**
     * @return array<string, mixed>
     */
    public function organization(): array
    {
        $legal = $this->settings->legalName();

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->settings->get('short_name'),
            'legalName' => $legal['name'],
            'url' => lroute('home'),
            'logo' => asset('images/logo-512.png'),
            'email' => $this->settings->get('email'),
            'foundingDate' => $this->settings->get('founding_year'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->settings->get('district'),
                'addressLocality' => $this->settings->get('city'),
                'addressCountry' => 'IQ',
            ],
            'sameAs' => array_values(array_filter([
                $this->settings->get('social_linkedin'),
                $this->settings->get('social_facebook'),
                $this->settings->get('social_instagram'),
            ])),
        ];

        return $this->withoutEmpty($data);
    }

    public function script(): HtmlString
    {
        $json = json_encode($this->organization(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);

        return new HtmlString('<script type="application/ld+json">'.$json.'</script>');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function withoutEmpty(array $data): array
    {
        return array_filter(
            array_map(fn (mixed $value): mixed => is_array($value) ? $this->withoutEmpty($value) : $value, $data),
            fn (mixed $value): bool => ! ($value === null || $value === '' || $value === []),
        );
    }
}
