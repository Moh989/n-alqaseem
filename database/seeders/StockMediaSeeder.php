<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Service;
use App\Services\Media\ImageUploadService;
use Illuminate\Database\Seeder;

/**
 * Processes the bundled licensed stock photographs through the image pipeline and
 * attaches them to services that do not have an image yet. Skipped during tests.
 */
class StockMediaSeeder extends Seeder
{
    public function run(ImageUploadService $images): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        foreach (require __DIR__.'/content/stock.php' as $key => $photo) {
            if (Media::where('seed_key', $key)->exists()) {
                continue;
            }

            $images->store(__DIR__.'/assets/stock/'.$photo['file'], [
                'seed_key' => $key,
                'alt_ar' => $photo['alt_ar'],
                'alt_en' => $photo['alt_en'],
                'is_stock' => true,
                'credit' => $photo['credit'],
                'source_url' => $photo['source_url'],
                'license' => $photo['license'],
                'created_by' => null,
            ]);

            $this->command?->getOutput()->writeln("  <info>✓</info> {$key}");
        }

        $services = collect((require __DIR__.'/content/services.php')['services'])->pluck('media', 'slug');

        foreach ($services as $slug => $mediaKey) {
            Service::where('slug', $slug)->whereNull('media_id')
                ->update(['media_id' => Media::where('seed_key', $mediaKey)->value('id')]);
        }
    }
}
