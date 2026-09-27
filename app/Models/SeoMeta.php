<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['page', 'title_ar', 'title_en', 'description_ar', 'description_en', 'og_media_id'])]
class SeoMeta extends Model
{
    use HasTranslations;

    /**
     * Pages with editable SEO metadata.
     *
     * @var array<string, string>
     */
    public const PAGES = [
        'home' => 'الرئيسية',
        'about' => 'عن الشركة',
        'services' => 'خدماتنا',
        'contact' => 'تواصل معنا',
        'privacy' => 'سياسة الخصوصية',
        'terms' => 'الشروط والإشعار القانوني',
    ];

    /**
     * @return BelongsTo<Media, $this>
     */
    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }
}
