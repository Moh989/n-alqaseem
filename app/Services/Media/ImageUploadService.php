<?php

namespace App\Services\Media;

use App\Models\Media;
use finfo;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Validates an uploaded raster image and re-encodes it into responsive WebP renditions,
 * a JPEG fallback and a 1200×630 social image. The original file is never kept, which
 * also strips metadata and any payload hidden in the upload.
 */
class ImageUploadService
{
    /**
     * @var array<string, int>
     */
    protected const ALLOWED = [
        'image/jpeg' => IMAGETYPE_JPEG,
        'image/png' => IMAGETYPE_PNG,
        'image/webp' => IMAGETYPE_WEBP,
    ];

    /**
     * @param  array<string, mixed>  $attributes  Extra Media attributes (alt texts, credit, seed key…)
     *
     * @throws ValidationException
     */
    public function store(UploadedFile|string $file, array $attributes = [], string $field = 'image', ?int $minWidth = null): Media
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $originalName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);

        if ($file instanceof UploadedFile && ! $file->isValid()) {
            $this->fail($field, 'invalid');
        }

        if (! is_file($path) || filesize($path) > config('site.images.max_kilobytes') * 1024) {
            $this->fail($field, 'too_large');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if (! array_key_exists($mime, self::ALLOWED)) {
            $this->fail($field, 'type');
        }

        $info = @getimagesize($path);

        if ($info === false || $info[2] !== self::ALLOWED[$mime]) {
            $this->fail($field, 'type');
        }

        [$width, $height] = $info;

        if ($width * $height > config('site.images.max_pixels')) {
            $this->fail($field, 'dimensions_large');
        }

        if ($width < ($minWidth ?? config('site.images.min_width'))) {
            $this->fail($field, 'dimensions_small', ['min' => $minWidth ?? config('site.images.min_width')]);
        }

        ini_set('memory_limit', '512M');

        $image = $this->decode($path, $mime);

        if ($image === null) {
            $this->fail($field, 'type');
        }

        return $this->writeRenditions($image, $originalName, $attributes);
    }

    protected function decode(string $path, string $mime): ?GdImage
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };

        if (! $image instanceof GdImage) {
            return null;
        }

        if ($mime === 'image/jpeg') {
            $image = $this->applyExifOrientation($image, $path);
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $image;
    }

    protected function applyExifOrientation(GdImage $image, string $path): GdImage
    {
        $exif = function_exists('exif_read_data') ? @exif_read_data($path) : false;
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

        return match ($orientation) {
            2 => $this->flipped($image, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($image, 180, 0) ?: $image,
            4 => $this->flipped($image, IMG_FLIP_VERTICAL),
            5 => $this->flipped(imagerotate($image, -90, 0) ?: $image, IMG_FLIP_HORIZONTAL),
            6 => imagerotate($image, -90, 0) ?: $image,
            7 => $this->flipped(imagerotate($image, 90, 0) ?: $image, IMG_FLIP_HORIZONTAL),
            8 => imagerotate($image, 90, 0) ?: $image,
            default => $image,
        };
    }

    protected function flipped(GdImage $image, int $mode): GdImage
    {
        imageflip($image, $mode);

        return $image;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function writeRenditions(GdImage $image, string $originalName, array $attributes): Media
    {
        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $disk = Storage::disk('public');
        $directory = 'media/'.now()->format('Y/m').'/'.Str::lower(Str::random(24));
        $disk->makeDirectory($directory);
        $absolute = $disk->path($directory);

        $widths = array_values(array_filter(config('site.images.widths'), fn (int $width): bool => $width <= $sourceWidth));

        if ($widths === []) {
            $widths = [$sourceWidth];
        }

        $bytes = 0;

        foreach ($widths as $width) {
            $resized = $this->resize($image, $width);
            imagewebp($resized, "$absolute/w$width.webp", config('site.images.webp_quality'));
            $bytes += (int) filesize("$absolute/w$width.webp");
        }

        $fallbackWidth = min(config('site.images.fallback_width'), $sourceWidth);
        $fallback = $this->flatten($this->resize($image, $fallbackWidth));
        imageinterlace($fallback, true);
        imagejpeg($fallback, "$absolute/fallback.jpg", config('site.images.jpeg_quality'));

        $og = $this->flatten($this->cover($image, 1200, 630));
        imagejpeg($og, "$absolute/og.jpg", 85);

        $largest = max($widths);

        return Media::create([
            ...$attributes,
            'directory' => $directory,
            'original_name' => mb_substr(Str::of($originalName)->replaceMatches('/[^\pL\pN._ -]+/u', '')->toString(), 0, 255),
            'width' => $largest,
            'height' => (int) round($sourceHeight * $largest / $sourceWidth),
            'bytes' => $bytes,
            'widths' => $widths,
            'created_by' => $attributes['created_by'] ?? auth()->id(),
        ]);
    }

    protected function resize(GdImage $image, int $width): GdImage
    {
        if ($width === imagesx($image)) {
            return $image;
        }

        $height = (int) round(imagesy($image) * $width / imagesx($image));
        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        return $canvas;
    }

    /**
     * Center-crop the image to fill the given box.
     */
    protected function cover(GdImage $image, int $width, int $height): GdImage
    {
        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $scale = max($width / $sourceWidth, $height / $sourceHeight);
        $cropWidth = (int) round($width / $scale);
        $cropHeight = (int) round($height / $scale);
        $x = (int) round(($sourceWidth - $cropWidth) / 2);
        $y = (int) round(($sourceHeight - $cropHeight) / 2);

        $canvas = imagecreatetruecolor($width, $height);
        imagecopyresampled($canvas, $image, 0, 0, $x, $y, $width, $height, $cropWidth, $cropHeight);

        return $canvas;
    }

    /**
     * Flatten transparency onto the brand cream background for JPEG output.
     */
    protected function flatten(GdImage $image): GdImage
    {
        $canvas = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 0xF7, 0xF4, 0xEE));
        imagealphablending($canvas, true);
        imagecopy($canvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

        return $canvas;
    }

    /**
     * @param  array<string, mixed>  $replace
     *
     * @throws ValidationException
     */
    protected function fail(string $field, string $reason, array $replace = []): never
    {
        throw ValidationException::withMessages([$field => __('admin.upload.'.$reason, $replace)]);
    }
}
