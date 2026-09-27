<?php

namespace App\Services\Media;

use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Publishes a company profile PDF from the file system (e.g. Web_profile.pdf in the project folder)
 * for both site languages, after the same security checks as an admin upload.
 */
class CompanyProfileImporter
{
    /**
     * File names looked for in the project folder, in order.
     *
     * @var list<string>
     */
    public const DEFAULT_FILES = ['Web_profile.pdf', 'web_profile.pdf'];

    public function __construct(protected PdfUploadService $pdfs, protected SiteSettings $settings) {}

    /**
     * Locate the default profile PDF in the project folder.
     */
    public static function defaultPath(): ?string
    {
        foreach (self::DEFAULT_FILES as $name) {
            if (is_file($path = base_path($name))) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Store the PDF for every locale and (optionally) approve it for publication.
     *
     * @param  list<string>  $locales
     *
     * @throws ValidationException
     */
    public function import(string $path, array $locales = ['ar', 'en'], bool $approve = true): void
    {
        foreach ($locales as $locale) {
            $setting = Setting::firstOrCreate(
                ['key' => 'profile_pdf_'.$locale],
                ['group' => 'documents', 'type' => 'file', 'status' => Setting::STATUS_PENDING],
            );

            $stored = $this->pdfs->store($path, $locale);
            $previous = $setting->value;

            $setting->update([
                'value' => $stored,
                'status' => $approve ? Setting::STATUS_APPROVED : Setting::STATUS_PENDING,
                'approved_at' => $approve ? now() : null,
                'approved_by' => null,
            ]);

            if ($previous && $previous !== $stored) {
                Storage::disk('local')->delete($previous);
            }
        }

        $this->settings->flush();
    }
}
