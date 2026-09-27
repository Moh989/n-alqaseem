<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Media;
use App\Services\Media\ImageUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HandlesImageUploads
{
    /**
     * Validation rules for an optional image upload with alt texts.
     *
     * @return array<string, list<string>>
     */
    protected function imageRules(string $field = 'image', bool $required = false): array
    {
        return [
            $field => [$required ? 'required' : 'nullable', 'file', 'max:'.config('site.images.max_kilobytes'), 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp'],
            'alt_ar' => ['nullable', 'string', 'max:255'],
            'alt_en' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Store a new image (if one was uploaded) on the given media attribute, releasing the old one,
     * or update the alt texts of the current image.
     */
    protected function syncImage(Request $request, Model $record, string $attribute = 'media_id', string $field = 'image', ?int $minWidth = null): void
    {
        $alt = array_filter(['alt_ar' => $request->input('alt_ar'), 'alt_en' => $request->input('alt_en')], fn (?string $value): bool => $value !== null);

        if ($request->hasFile($field)) {
            $previous = $record->{$attribute} ? Media::find($record->{$attribute}) : null;
            $media = app(ImageUploadService::class)->store($request->file($field), $alt, $field, $minWidth);

            $record->forceFill([$attribute => $media->id])->save();
            $previous?->deleteIfUnused();

            return;
        }

        if ($record->{$attribute} && $alt !== []) {
            Media::whereKey($record->{$attribute})->update($alt);
        }
    }
}
