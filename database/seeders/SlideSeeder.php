<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Slide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class SlideSeeder extends Seeder
{
    public function run(): void
    {
        foreach (require __DIR__.'/content/slides.php' as $slide) {
            $record = Slide::firstOrCreate(['seed_key' => $slide['seed_key']], [
                ...Arr::except($slide, ['media']),
                'is_published' => true,
            ]);

            if ($record->media_id === null && ($mediaId = Media::where('seed_key', $slide['media'])->value('id'))) {
                $record->update(['media_id' => $mediaId]);
            }
        }
    }
}
