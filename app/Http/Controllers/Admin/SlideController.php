<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Media;
use App\Models\Slide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SlideController extends Controller
{
    use HandlesImageUploads, ReordersRecords;

    public function index(Request $request): View
    {
        $page = array_key_exists($request->query('page'), Slide::PAGES) ? $request->query('page') : 'home';

        return view('admin.slides.index', [
            'page' => $page,
            'slides' => Slide::with('media')->where('page', $page)->orderBy('sort')->orderBy('id')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.slides.form', [
            'slide' => new Slide(['page' => array_key_exists($request->query('page'), Slide::PAGES) ? $request->query('page') : 'home', 'is_published' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(true));

        $slide = DB::transaction(function () use ($request, $data): Slide {
            $slide = Slide::create([
                ...Arr::except($data, ['image', 'alt_ar', 'alt_en']),
                'sort' => (int) Slide::where('page', $data['page'])->max('sort') + 1,
                'is_published' => $request->boolean('is_published'),
            ]);
            $this->syncImage($request, $slide, minWidth: 1280);

            return $slide;
        });

        AuditLog::record('slide.create', $slide, $slide->heading_ar);

        return redirect()->route('admin.slides.index', ['page' => $slide->page])->with('status', __('admin.flash.created'));
    }

    public function edit(Slide $slide): View
    {
        return view('admin.slides.form', ['slide' => $slide->load('media')]);
    }

    public function update(Request $request, Slide $slide): RedirectResponse
    {
        $data = $request->validate($this->rules(false));

        $slide->update([
            ...Arr::except($data, ['image', 'alt_ar', 'alt_en']),
            'is_published' => $request->boolean('is_published'),
        ]);
        $this->syncImage($request, $slide, minWidth: 1280);

        AuditLog::record('slide.update', $slide, $slide->heading_ar);

        return redirect()->route('admin.slides.index', ['page' => $slide->page])->with('status', __('admin.flash.saved'));
    }

    public function destroy(Slide $slide): RedirectResponse
    {
        $media = $slide->media;
        AuditLog::record('slide.delete', $slide, $slide->heading_ar);
        $slide->delete();

        if ($media instanceof Media && ! $media->is_stock) {
            $media->deleteIfUnused();
        }

        return redirect()->route('admin.slides.index', ['page' => $slide->page])->with('status', __('admin.flash.deleted'));
    }

    public function move(Slide $slide, string $direction): RedirectResponse
    {
        $this->moveRecord($slide, $direction, Slide::where('page', $slide->page));

        return redirect()->route('admin.slides.index', ['page' => $slide->page])->with('status', __('admin.flash.moved'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(bool $creating): array
    {
        $targets = array_keys(config('site.cta_targets'));

        return [
            'page' => ['required', Rule::in(array_keys(Slide::PAGES))],
            'heading_ar' => ['required', 'string', 'max:160'],
            'heading_en' => ['required_if_accepted:is_published', 'nullable', 'string', 'max:160'],
            'text_ar' => ['nullable', 'string', 'max:400'],
            'text_en' => ['nullable', 'string', 'max:400'],
            'cta1_label_ar' => ['nullable', 'string', 'max:60'],
            'cta1_label_en' => ['nullable', 'string', 'max:60'],
            'cta1_target' => ['nullable', Rule::in($targets)],
            'cta2_label_ar' => ['nullable', 'string', 'max:60'],
            'cta2_label_en' => ['nullable', 'string', 'max:60'],
            'cta2_target' => ['nullable', Rule::in($targets)],
            'is_published' => ['nullable', 'boolean'],
            ...$this->imageRules('image', $creating),
        ];
    }
}
