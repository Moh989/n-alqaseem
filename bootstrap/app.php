<?php

use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\NoStore;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'locale' => SetLocale::class,
            'active' => EnsureActiveUser::class,
            'no-store' => NoStore::class,
        ]);

        $middleware->web(append: [SecurityHeaders::class]);

        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Too many contact submissions: go back to the form with a friendly message.
        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            if ($request->routeIs('contact.store', 'en.contact.store')) {
                SetLocale::apply($request->segment(1) === 'en' ? 'en' : 'ar');

                return back()->withInput()->with('contact_error', __('contact.throttled'));
            }

            return null;
        });

        // Uploads larger than the server's post_max_size.
        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            SetLocale::apply('ar');

            return response()->view('errors.413', [], 413);
        });

        // Error pages are rendered in the language of the requested URL.
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            SetLocale::apply($request->segment(1) === 'en' && ! $request->is('admin*') ? 'en' : 'ar');

            return null;
        });
    })->create();
