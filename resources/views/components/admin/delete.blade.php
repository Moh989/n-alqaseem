@props(['action', 'label' => null, 'confirm' => null])
<form method="POST" action="{{ $action }}" data-confirm="{{ $confirm ?? __('admin.common.confirm_delete') }}" {{ $attributes->class(['inline-form']) }}>
    @csrf
    @method('DELETE')
    <button class="btn btn--danger btn--sm" type="submit"><x-icon name="trash-2" /> {{ $label ?? __('admin.common.delete') }}</button>
</form>
