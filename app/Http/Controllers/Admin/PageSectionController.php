<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    use ReordersRecords;

    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => PageSection::PAGES,
            'counts' => PageSection::selectRaw('page, count(*) as total')->groupBy('page')->pluck('total', 'page'),
        ]);
    }

    public function edit(string $page): View
    {
        abort_unless(array_key_exists($page, PageSection::PAGES), 404);

        return view('admin.pages.edit', [
            'page' => $page,
            'pageInfo' => PageSection::PAGES[$page],
            'sections' => PageSection::with('items')->where('page', $page)->orderBy('sort')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request, string $page): RedirectResponse
    {
        abort_unless(isset(PageSection::PAGES[$page]) && ! PageSection::PAGES[$page]['fixed'], 404);

        $data = $request->validate($this->rules());

        $section = PageSection::create([
            ...$data,
            'page' => $page,
            'key' => 'section-'.Str::lower(Str::random(6)),
            'sort' => (int) PageSection::where('page', $page)->max('sort') + 1,
            'is_published' => $request->boolean('is_published'),
        ]);

        AuditLog::record('section.create', $section, $page);

        return redirect()->route('admin.pages.edit', $page)->with('status', __('admin.flash.created'));
    }

    public function update(Request $request, PageSection $section): RedirectResponse
    {
        $section->update([
            ...$request->validate($this->rules()),
            'is_published' => $request->boolean('is_published'),
        ]);

        AuditLog::record('section.update', $section, $section->page.'/'.$section->key);

        return redirect()->to(route('admin.pages.edit', $section->page).'#section-'.$section->id)->with('status', __('admin.flash.saved'));
    }

    public function destroy(PageSection $section): RedirectResponse
    {
        abort_if(PageSection::PAGES[$section->page]['fixed'] ?? true, 403);

        $page = $section->page;
        AuditLog::record('section.delete', $section, $page.'/'.$section->key);
        $section->delete();

        return redirect()->route('admin.pages.edit', $page)->with('status', __('admin.flash.deleted'));
    }

    public function move(PageSection $section, string $direction): RedirectResponse
    {
        $this->moveRecord($section, $direction, PageSection::where('page', $section->page));

        return redirect()->to(route('admin.pages.edit', $section->page).'#section-'.$section->id)->with('status', __('admin.flash.moved'));
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'body_ar' => ['nullable', 'string', 'max:20000'],
            'body_en' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
