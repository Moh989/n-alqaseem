@props(['checked' => false, 'name' => 'is_published', 'label' => null])
<label {{ $attributes->class(['switch']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked))>
    <span class="switch__track" aria-hidden="true"></span>
    <span>{{ $label ?? __('admin.common.publish') }}</span>
</label>
