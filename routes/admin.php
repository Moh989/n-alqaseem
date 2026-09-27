<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\ProfilePdfController;
use App\Http\Controllers\Admin\SectionItemController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel (Arabic, RTL)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['locale:ar', 'no-store'])->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'active'])->group(function (): void {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings/{group}', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/pages', [PageSectionController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}', [PageSectionController::class, 'edit'])->name('pages.edit');
        Route::post('/pages/{page}/sections', [PageSectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}', [PageSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [PageSectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/{section}/move/{direction}', [PageSectionController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('sections.move');

        Route::post('/sections/{section}/items', [SectionItemController::class, 'store'])->name('items.store');
        Route::put('/items/{item}', [SectionItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [SectionItemController::class, 'destroy'])->name('items.destroy');
        Route::post('/items/{item}/move/{direction}', [SectionItemController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('items.move');

        Route::resource('slides', SlideController::class)->except('show');
        Route::post('/slides/{slide}/move/{direction}', [SlideController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('slides.move');

        Route::resource('categories', ServiceCategoryController::class)->except('show')->parameters(['categories' => 'category']);
        Route::resource('services', ServiceController::class)->except('show');
        Route::post('/services/{service}/move/{direction}', [ServiceController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('services.move');

        Route::get('/seo', [SeoController::class, 'index'])->name('seo.index');
        Route::get('/seo/{page}', [SeoController::class, 'edit'])->name('seo.edit');
        Route::put('/seo/{page}', [SeoController::class, 'update'])->name('seo.update');

        Route::get('/profile-pdf', [ProfilePdfController::class, 'index'])->name('profile-pdf.index');
        Route::post('/profile-pdf/{locale}', [ProfilePdfController::class, 'store'])->whereIn('locale', ['ar', 'en'])->name('profile-pdf.store');
        Route::put('/profile-pdf/{locale}', [ProfilePdfController::class, 'update'])->whereIn('locale', ['ar', 'en'])->name('profile-pdf.update');
        Route::delete('/profile-pdf/{locale}', [ProfilePdfController::class, 'destroy'])->whereIn('locale', ['ar', 'en'])->name('profile-pdf.destroy');

        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{message}/archive', [ContactMessageController::class, 'archive'])->name('messages.archive');
        Route::post('/messages/{message}/unarchive', [ContactMessageController::class, 'unarchive'])->name('messages.unarchive');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    });
});
