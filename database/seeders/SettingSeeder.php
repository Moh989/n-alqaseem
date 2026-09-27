<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (require __DIR__.'/content/settings.php' as $sort => $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], [
                ...$setting,
                'sort' => $sort,
                'approved_at' => $setting['status'] === Setting::STATUS_APPROVED ? now() : null,
            ]);
        }

        app(SiteSettings::class)->flush();
    }
}
