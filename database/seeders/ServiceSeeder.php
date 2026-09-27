<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $content = require __DIR__.'/content/services.php';

        $categories = collect($content['categories'])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => ServiceCategory::firstOrCreate(['slug' => $category['slug']], $category),
        ]);

        foreach ($content['services'] as $service) {
            Service::firstOrCreate(['slug' => $service['slug']], [
                ...Arr::except($service, ['category', 'media']),
                'service_category_id' => $categories[$service['category']]->id,
                'is_published' => true,
            ]);
        }
    }
}
