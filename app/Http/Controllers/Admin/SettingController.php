<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Groups editable on this screen (documents are handled on the profile PDF screen).
     *
     * @var list<string>
     */
    protected const GROUPS = ['company', 'contact', 'social', 'legal', 'notifications'];

    public function index(): View
    {
        return view('admin.settings.index', [
            'groups' => Setting::whereIn('group', self::GROUPS)->orderBy('sort')->get()->groupBy('group')
                ->sortBy(fn ($settings, string $group): int => array_search($group, self::GROUPS, true)),
        ]);
    }

    public function update(Request $request, string $group, SiteSettings $siteSettings): RedirectResponse
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        $settings = Setting::where('group', $group)->get();
        $request->validate($this->rules($settings));

        foreach ($settings as $setting) {
            $input = $request->input('settings.'.$setting->key, []);

            if ($setting->is_translatable) {
                $setting->value_ar = $this->clean($input['value_ar'] ?? null);
                $setting->value_en = $this->clean($input['value_en'] ?? null);
            } elseif ($setting->type !== 'boolean') {
                $setting->value = $this->clean($input['value'] ?? null);
            }

            $approve = ! empty($input['approved']);
            $wasApproved = $setting->isApproved();
            $setting->status = $approve ? Setting::STATUS_APPROVED : Setting::STATUS_PENDING;

            if ($approve && ! $wasApproved) {
                $setting->approved_at = now();
                $setting->approved_by = $request->user()->id;
            }

            if ($setting->isDirty()) {
                $setting->save();
                AuditLog::record('setting.update', $setting, $setting->key.' → '.$setting->status);
            }
        }

        $siteSettings->flush();

        return redirect()->to(route('admin.settings.index').'#group-'.$group)->with('status', __('admin.flash.saved'));
    }

    /**
     * @param  iterable<Setting>  $settings
     * @return array<string, list<string>>
     */
    protected function rules(iterable $settings): array
    {
        $rules = [];

        foreach ($settings as $setting) {
            $base = 'settings.'.$setting->key;
            $valueRule = match ($setting->type) {
                'email' => ['nullable', 'email', 'max:191'],
                'url' => ['nullable', 'url:https', 'max:500'],
                'year' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:'.now()->year],
                'textarea' => ['nullable', 'string', 'max:1000'],
                default => ['nullable', 'string', 'max:255'],
            };

            if ($setting->is_translatable) {
                $rules[$base.'.value_ar'] = $valueRule;
                $rules[$base.'.value_en'] = $valueRule;
            } else {
                $rules[$base.'.value'] = $valueRule;
            }

            $rules[$base.'.approved'] = ['nullable', 'boolean'];
        }

        return $rules;
    }

    protected function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
