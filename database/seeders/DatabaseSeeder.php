<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the site's initial, verified content.
     *
     * Seeders are idempotent: existing records (possibly edited in the admin panel)
     * are never overwritten. No administrator account is created here — use
     * `php artisan admin:create`.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            PageSectionSeeder::class,
            ServiceSeeder::class,
            SeoSeeder::class,
            StockMediaSeeder::class,
            SlideSeeder::class,
            ProfilePdfSeeder::class,
        ]);
    }
}
