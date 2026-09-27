<?php

namespace App\Models\Concerns;

/**
 * Reads paired "{field}_ar" / "{field}_en" columns for the active locale.
 */
trait HasTranslations
{
    /**
     * Get the value of a bilingual field for the given (or current) locale.
     *
     * No cross-language fallback is applied, so an English page never shows Arabic copy by accident.
     */
    public function tr(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $value = $this->getAttribute($field.'_'.$locale);

        return filled($value) ? (string) $value : null;
    }

    /**
     * Determine whether both language versions of a field are filled.
     */
    public function hasBothLanguages(string $field): bool
    {
        return filled($this->getAttribute($field.'_ar')) && filled($this->getAttribute($field.'_en'));
    }
}
