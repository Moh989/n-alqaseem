<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (require __DIR__.'/content/pages.php' as $page => $sections) {
            foreach ($sections as $sort => $data) {
                $items = $data['items'] ?? [];
                unset($data['items']);

                $section = PageSection::firstOrCreate(
                    ['page' => $page, 'key' => $data['key']],
                    [...$data, 'sort' => $sort + 1, 'is_published' => true],
                );

                if ($section->wasRecentlyCreated) {
                    foreach ($items as $index => $item) {
                        $section->items()->create([...$item, 'sort' => $index + 1]);
                    }
                }
            }
        }
    }
}
