<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name_ar', 'name_en', 'description_ar', 'description_en', 'sort'])]
class ServiceCategory extends Model
{
    use HasTranslations;

    /**
     * @return HasMany<Service, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('sort')->orderBy('id');
    }

    /**
     * @return HasMany<Service, $this>
     */
    public function publishedServices(): HasMany
    {
        return $this->services()->where('is_published', true);
    }
}
