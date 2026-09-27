<?php

namespace Database\Seeders;

use App\Models\SeoMeta;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (require __DIR__.'/content/seo.php' as $meta) {
            SeoMeta::firstOrCreate(['page' => $meta['page']], $meta);
        }
    }
}
