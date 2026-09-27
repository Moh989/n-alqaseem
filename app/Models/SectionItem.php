<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['page_section_id', 'title_ar', 'title_en', 'text_ar', 'text_en', 'icon', 'sort'])]
class SectionItem extends Model
{
    use HasTranslations;

    /**
     * @return BelongsTo<PageSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }
}
