<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PageSection;
use App\Models\SectionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionItemController extends Controller
{
    use ReordersRecords;

    /**
     * Icons offered for list items (lucide names bundled with the site).
     *
     * @var list<string>
     */
    public const ICONS = ['handshake', 'users', 'badge-check', 'briefcase', 'shield-check', 'lightbulb', 'hard-hat', 'route', 'building-2', 'package', 'search', 'pencil-ruler', 'drafting-compass', 'wrench', 'clipboard-list', 'calendar-range', 'flag', 'landmark', 'scale', 'folder-cog'];

    public function store(Request $request, PageSection $section): RedirectResponse
    {
        $item = $section->items()->create([
            ...$request->validate($this->rules()),
            'sort' => (int) $section->items()->max('sort') + 1,
        ]);

        AuditLog::record('item.create', $item, $section->page.'/'.$section->key);

        return $this->back($section)->with('status', __('admin.flash.created'));
    }

    public function update(Request $request, SectionItem $item): RedirectResponse
    {
        $item->update($request->validate($this->rules()));
        AuditLog::record('item.update', $item);

        return $this->back($item->section)->with('status', __('admin.flash.saved'));
    }

    public function destroy(SectionItem $item): RedirectResponse
    {
        $section = $item->section;
        AuditLog::record('item.delete', $item, $item->title_ar);
        $item->delete();

        return $this->back($section)->with('status', __('admin.flash.deleted'));
    }

    public function move(SectionItem $item, string $direction): RedirectResponse
    {
        $this->moveRecord($item, $direction, SectionItem::where('page_section_id', $item->page_section_id));

        return $this->back($item->section)->with('status', __('admin.flash.moved'));
    }

    protected function back(PageSection $section): RedirectResponse
    {
        return redirect()->to(route('admin.pages.edit', $section->page).'#section-'.$section->id);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'text_ar' => ['nullable', 'string', 'max:2000'],
            'text_en' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', Rule::in(self::ICONS)],
        ];
    }
}
