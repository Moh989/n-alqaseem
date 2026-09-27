<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Read access to site settings. Only approved, non-empty values are ever returned
 * for public use; everything else is treated as "pending approval".
 */
class SiteSettings
{
    protected const CACHE_KEY = 'site.settings.v1';

    /**
     * @var array<string, array{status: string, translatable: bool, value: ?string, value_ar: ?string, value_en: ?string}>|null
     */
    protected ?array $settings = null;

    /**
     * Get an approved setting value for the given (or current) locale.
     */
    public function get(string $key, ?string $locale = null): ?string
    {
        $setting = $this->all()[$key] ?? null;

        if ($setting === null || $setting['status'] !== Setting::STATUS_APPROVED) {
            return null;
        }

        $value = $setting['translatable']
            ? $setting['value_'.($locale ?? app()->getLocale())]
            : $setting['value'];

        return filled($value) ? (string) $value : null;
    }

    public function has(string $key, ?string $locale = null): bool
    {
        return $this->get($key, $locale) !== null;
    }

    /**
     * The company's legal name for display in the current locale.
     *
     * The English legal name is shown only once approved; otherwise the approved Arabic name is used.
     *
     * @return array{name: ?string, lang: string}
     */
    public function legalName(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        if ($locale === 'en' && ($english = $this->get('legal_name_en'))) {
            return ['name' => $english, 'lang' => 'en'];
        }

        return ['name' => $this->get('legal_name_ar'), 'lang' => 'ar'];
    }

    /**
     * Stored path of the approved company profile PDF for a locale, falling back to the other language.
     */
    public function profilePdfPath(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $this->get('profile_pdf_'.$locale)
            ?? $this->get('profile_pdf_'.($locale === 'ar' ? 'en' : 'ar'));
    }

    public function hasProfilePdf(?string $locale = null): bool
    {
        return $this->profilePdfPath($locale) !== null;
    }

    /**
     * Where the "Company profile" buttons lead: the PDF when one is published, otherwise the About page.
     */
    public function profileUrl(?string $locale = null): string
    {
        return $this->hasProfilePdf($locale)
            ? lroute('profile.download', [], $locale)
            : lroute('about', [], $locale);
    }

    public function mapsUrl(): ?string
    {
        $query = $this->get('maps_query');

        return $query ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($query) : null;
    }

    /**
     * @return array<string, array{status: string, translatable: bool, value: ?string, value_ar: ?string, value_en: ?string}>
     */
    public function all(): array
    {
        return $this->settings ??= $this->load();
    }

    public function flush(): void
    {
        $this->settings = null;
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array{status: string, translatable: bool, value: ?string, value_ar: ?string, value_en: ?string}>
     */
    protected function load(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn (): array => Setting::query()
                ->get(['key', 'status', 'is_translatable', 'value', 'value_ar', 'value_en'])
                ->mapWithKeys(fn (Setting $setting): array => [$setting->key => [
                    'status' => $setting->status,
                    'translatable' => $setting->is_translatable,
                    'value' => $setting->value,
                    'value_ar' => $setting->value_ar,
                    'value_en' => $setting->value_en,
                ]])
                ->all());
        } catch (Throwable $exception) {
            if (! Schema::hasTable('settings')) {
                return [];
            }

            throw $exception;
        }
    }
}
