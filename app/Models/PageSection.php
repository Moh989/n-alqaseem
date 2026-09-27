<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['page', 'key', 'title_ar', 'title_en', 'body_ar', 'body_en', 'sort', 'is_published'])]
class PageSection extends Model
{
    use HasTranslations;

    /**
     * Pages whose texts are managed through page sections.
     *
     * @var array<string, array{ar: string, en: string, fixed: bool}>
     */
    public const PAGES = [
        'home' => ['ar' => 'الرئيسية', 'en' => 'Home', 'fixed' => true],
        'about' => ['ar' => 'عن الشركة', 'en' => 'About', 'fixed' => true],
        'services' => ['ar' => 'خدماتنا', 'en' => 'Services', 'fixed' => true],
        'contact' => ['ar' => 'تواصل معنا', 'en' => 'Contact', 'fixed' => true],
        'privacy' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy policy', 'fixed' => false],
        'terms' => ['ar' => 'الشروط والإشعار القانوني', 'en' => 'Terms & legal notice', 'fixed' => false],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    /**
     * @return HasMany<SectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort')->orderBy('id');
    }
}
