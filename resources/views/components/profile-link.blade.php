{{-- "Company profile" link: opens the published PDF in a new tab, or the About page while none is published. --}}
@php($hasPdf = site()->hasProfilePdf())
<a href="{{ site()->profileUrl() }}"
   {{ $attributes }}
   @if ($hasPdf) target="_blank" rel="noopener" type="application/pdf" @endif>{{ $slot }}@if ($hasPdf)<span class="visually-hidden"> {{ __('site.actions.opens_new_tab') }}</span>@endif</a>
