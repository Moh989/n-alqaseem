<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\Media\PdfUploadService;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilePdfController extends Controller
{
    public function index(): View
    {
        return view('admin.profile-pdf.index', [
            'files' => collect(['ar', 'en'])->mapWithKeys(fn (string $locale): array => [
                $locale => Setting::firstOrCreate(
                    ['key' => 'profile_pdf_'.$locale],
                    ['group' => 'documents', 'type' => 'file', 'status' => Setting::STATUS_PENDING, 'label_ar' => 'الملف التعريفي PDF ('.($locale === 'ar' ? 'عربي' : 'إنجليزي').')'],
                ),
            ]),
        ]);
    }

    public function store(Request $request, string $locale, PdfUploadService $pdfs, SiteSettings $settings): RedirectResponse
    {
        $request->validate([
            'pdf' => ['required', 'file', 'max:'.config('site.pdf.max_kilobytes'), 'mimes:pdf', 'mimetypes:application/pdf'],
        ]);

        $setting = Setting::where('key', 'profile_pdf_'.$locale)->firstOrFail();
        $path = $pdfs->store($request->file('pdf'), $locale);
        $previous = $setting->value;

        // A new file always requires a fresh approval before it is published.
        $setting->update(['value' => $path, 'status' => Setting::STATUS_PENDING, 'approved_at' => null, 'approved_by' => null]);

        if ($previous && $previous !== $path) {
            Storage::disk('local')->delete($previous);
        }

        $settings->flush();
        AuditLog::record('profile-pdf.upload', $setting, $locale);

        return redirect()->route('admin.profile-pdf.index')->with('status', __('admin.flash.uploaded'));
    }

    public function update(Request $request, string $locale, SiteSettings $settings): RedirectResponse
    {
        $setting = Setting::where('key', 'profile_pdf_'.$locale)->firstOrFail();
        $approve = $request->boolean('approved') && filled($setting->value);

        $setting->update([
            'status' => $approve ? Setting::STATUS_APPROVED : Setting::STATUS_PENDING,
            'approved_at' => $approve ? now() : null,
            'approved_by' => $approve ? $request->user()->id : null,
        ]);

        $settings->flush();
        AuditLog::record('profile-pdf.status', $setting, $locale.' → '.$setting->status);

        return redirect()->route('admin.profile-pdf.index')->with('status', __('admin.flash.saved'));
    }

    public function destroy(string $locale, SiteSettings $settings): RedirectResponse
    {
        $setting = Setting::where('key', 'profile_pdf_'.$locale)->firstOrFail();

        if ($setting->value) {
            Storage::disk('local')->delete($setting->value);
        }

        $setting->update(['value' => null, 'status' => Setting::STATUS_PENDING, 'approved_at' => null, 'approved_by' => null]);
        $settings->flush();
        AuditLog::record('profile-pdf.delete', $setting, $locale);

        return redirect()->route('admin.profile-pdf.index')->with('status', __('admin.flash.deleted'));
    }
}
