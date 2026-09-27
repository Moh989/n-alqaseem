<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['seed_key', 'page', 'media_id', 'heading_ar', 'heading_en', 'text_ar', 'text_en', 'cta1_label_ar', 'cta1_label_en', 'cta1_target', 'cta2_label_ar', 'cta2_label_en', 'cta2_target', 'sort', 'is_published'])]
class Slide extends Model
{
    use HasTranslations;

    /**
     * Pages that display a slider.
     *
     * @var array<string, string>
     */
    public const PAGES = ['home' => 'الرئيسية', 'about' => 'عن الشركة', 'services' => 'خدماتنا'];

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
     * @return BelongsTo<Media, $this>
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Resolve the whitelisted call-to-action buttons for the current locale.
     *
     * The "profile" target opens the company profile PDF in a new tab (or the About page
     * while no PDF is published).
     *
     * @return list<array{label: string, url: string, new_tab: bool}>
     */
    public function ctas(): array
    {
        $targets = config('site.cta_targets');
        $buttons = [];

        foreach ([1, 2] as $index) {
            $target = $this->getAttribute("cta{$index}_target");
            $label = $this->tr("cta{$index}_label");

            if (! $label || ! isset($targets[$target])) {
                continue;
            }

            $isProfile = $target === 'profile';

            $buttons[] = [
                'label' => $label,
                'url' => $isProfile ? site()->profileUrl() : lroute($targets[$target]),
                'new_tab' => $isProfile && site()->hasProfilePdf(),
            ];
        }

        return $buttons;
    }
}
