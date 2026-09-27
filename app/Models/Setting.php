<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['key', 'group', 'type', 'is_translatable', 'value', 'value_ar', 'value_en', 'status', 'approved_at', 'approved_by', 'label_ar', 'label_en', 'admin_note', 'sort'])]
class Setting extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_translatable' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Get the stored value for a locale (translatable settings) or the single value.
     */
    public function valueFor(?string $locale = null): ?string
    {
        $value = $this->is_translatable
            ? $this->getAttribute('value_'.($locale ?? app()->getLocale()))
            : $this->value;

        return filled($value) ? (string) $value : null;
    }

    /**
     * Determine whether the setting has content in every language it needs.
     */
    public function isFilled(): bool
    {
        return $this->is_translatable
            ? filled($this->value_ar) && filled($this->value_en)
            : filled($this->value);
    }

    public function label(): string
    {
        return $this->label_ar ?: $this->key;
    }
}
