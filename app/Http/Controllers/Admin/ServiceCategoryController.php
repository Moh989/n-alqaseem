<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => ServiceCategory::withCount('services')->orderBy('sort')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new ServiceCategory]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = ServiceCategory::create([
            ...$request->validate($this->rules()),
            'sort' => (int) ServiceCategory::max('sort') + 1,
        ]);

        AuditLog::record('category.create', $category, $category->name_ar);

        return redirect()->route('admin.categories.index')->with('status', __('admin.flash.created'));
    }

    public function edit(ServiceCategory $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(Request $request, ServiceCategory $category): RedirectResponse
    {
        $category->update($request->validate($this->rules($category)));
        AuditLog::record('category.update', $category, $category->name_ar);

        return redirect()->route('admin.categories.index')->with('status', __('admin.flash.saved'));
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        if ($category->services()->exists()) {
            return back()->with('error', __('admin.flash.category_in_use'));
        }

        AuditLog::record('category.delete', $category, $category->name_ar);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', __('admin.flash.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(?ServiceCategory $category = null): array
    {
        return [
            'slug' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('service_categories', 'slug')->ignore($category)],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:1000'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
