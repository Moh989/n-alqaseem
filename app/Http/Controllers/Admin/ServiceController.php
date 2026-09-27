<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Media;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceController extends Controller
{
    use HandlesImageUploads, ReordersRecords;

    /**
     * Icons offered for services (lucide names bundled with the site).
     *
     * @var list<string>
     */
    public const ICONS = ['building-2', 'route', 'truck', 'fuel', 'layers', 'droplet', 'package', 'wrench', 'ship', 'boxes', 'hard-hat', 'factory', 'anchor', 'container', 'warehouse', 'cog'];

    public function index(): View
    {
        return view('admin.services.index', [
            'categories' => ServiceCategory::with(['services.media'])->orderBy('sort')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', [
            'service' => new Service(['is_published' => false]),
            'categories' => ServiceCategory::orderBy('sort')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $service = Service::create([
            ...Arr::except($data, ['image', 'alt_ar', 'alt_en']),
            'sort' => (int) Service::max('sort') + 1,
            'is_published' => $request->boolean('is_published'),
        ]);
        $this->syncImage($request, $service);

        AuditLog::record('service.create', $service, $service->title_ar);

        return redirect()->route('admin.services.edit', $service)->with('status', __('admin.flash.created'));
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', [
            'service' => $service->load('media'),
            'categories' => ServiceCategory::orderBy('sort')->get(),
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate($this->rules($service));

        $service->update([
            ...Arr::except($data, ['image', 'alt_ar', 'alt_en']),
            'is_published' => $request->boolean('is_published'),
        ]);
        $this->syncImage($request, $service);

        AuditLog::record('service.update', $service, $service->title_ar);

        return redirect()->route('admin.services.edit', $service)->with('status', __('admin.flash.saved'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        $media = $service->media;
        AuditLog::record('service.delete', $service, $service->title_ar);
        $service->delete();

        if ($media instanceof Media && ! $media->is_stock) {
            $media->deleteIfUnused();
        }

        return redirect()->route('admin.services.index')->with('status', __('admin.flash.deleted'));
    }

    public function move(Service $service, string $direction): RedirectResponse
    {
        $this->moveRecord($service, $direction, Service::where('service_category_id', $service->service_category_id));

        return redirect()->route('admin.services.index')->with('status', __('admin.flash.moved'));
    }

    /**
     * Publishing requires the title, summary and body in both languages.
     *
     * @return array<string, mixed>
     */
    protected function rules(?Service $service = null): array
    {
        $whenPublished = ['required_if_accepted:is_published', 'nullable', 'string'];

        return [
            'service_category_id' => ['required', 'integer', Rule::exists('service_categories', 'id')],
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('services', 'slug')->ignore($service)],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => [...$whenPublished, 'max:255'],
            'summary_ar' => [...$whenPublished, 'max:500'],
            'summary_en' => [...$whenPublished, 'max:500'],
            'body_ar' => [...$whenPublished, 'max:20000'],
            'body_en' => [...$whenPublished, 'max:20000'],
            'icon' => ['nullable', Rule::in(self::ICONS)],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_description_ar' => ['nullable', 'string', 'max:320'],
            'seo_description_en' => ['nullable', 'string', 'max:320'],
            'is_published' => ['nullable', 'boolean'],
            ...$this->imageRules(),
        ];
    }
}
