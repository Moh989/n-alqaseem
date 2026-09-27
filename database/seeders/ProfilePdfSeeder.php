<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Media\CompanyProfileImporter;
use Illuminate\Database\Seeder;

/**
 * Publishes Web_profile.pdf from the project folder when no profile PDF has been set yet.
 * Existing files (e.g. uploaded from the admin panel) are never replaced. Skipped during tests.
 */
class ProfilePdfSeeder extends Seeder
{
    public function run(CompanyProfileImporter $importer): void
    {
        if (app()->runningUnitTests() || ! ($path = CompanyProfileImporter::defaultPath())) {
            return;
        }

        $missing = collect(['ar', 'en'])
            ->filter(fn (string $locale): bool => blank(Setting::where('key', 'profile_pdf_'.$locale)->value('value')))
            ->values()
            ->all();

        if ($missing !== []) {
            $importer->import($path, $missing);
        }
    }
}
