<?php

namespace App\Providers;

use App\Services\Seo\SeoManager;
use App\Support\SiteSettings;
use App\View\Composers\SiteComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SiteSettings::class);
        $this->app->scoped(SeoManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Let `composer dev` pass the local upload limits (.dev/uploads.ini) to `php -S`.
        ServeCommand::$passthroughVariables[] = 'PHP_INI_SCAN_DIR';

        RateLimiter::for('contact', fn (Request $request): array => [
            Limit::perMinute(config('site.contact.per_minute'))->by('contact-minute:'.$request->ip()),
            Limit::perDay(config('site.contact.per_day'))->by('contact-day:'.$request->ip()),
        ]);

        View::composer(['layouts.site', 'errors::*', 'errors.*'], SiteComposer::class);
    }
}
