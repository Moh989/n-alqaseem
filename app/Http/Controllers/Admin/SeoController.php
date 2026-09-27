<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SeoMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class SeoController extends Controller
{
    use HandlesImageUploads;

    public function index(): View
    {
        return view('admin.seo.index', [
            'pages' => SeoMeta::PAGES,
            'metas' => SeoMeta::with('ogMedia')->get()->keyBy('page'),
        ]);
    }

    public function edit(string $page): View
    {
        abort_unless(array_key_exists($page, SeoMeta::PAGES), 404);

        return view('admin.seo.form', [
            'page' => $page,
            'meta' => SeoMeta::with('ogMedia')->firstOrNew(['page' => $page]),
        ]);
    }

    public function update(Request $request, string $page): RedirectResponse
    {
        abort_unless(array_key_exists($page, SeoMeta::PAGES), 404);

        $data = $request->validate([
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:320'],
            'description_en' => ['nullable', 'string', 'max:320'],
            ...$this->imageRules('og_image'),
        ]);

        $meta = SeoMeta::updateOrCreate(['page' => $page], Arr::except($data, ['og_image', 'alt_ar', 'alt_en']));
        $this->syncImage($request, $meta, 'og_media_id', 'og_image', 600);

        AuditLog::record('seo.update', $meta, $page);

        return redirect()->route('admin.seo.edit', $page)->with('status', __('admin.flash.saved'));
    }
}
