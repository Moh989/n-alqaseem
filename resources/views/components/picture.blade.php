@props(['media' => null, 'sizes' => '100vw', 'eager' => false, 'alt' => null, 'imgClass' => null])
@if ($media)
    @php($fallbackWidth = min(config('site.images.fallback_width'), $media->largestWidth()))
    <picture {{ $attributes }}>
        <source type="image/webp" srcset="{{ $media->srcset() }}" sizes="{{ $sizes }}">
        <img
            src="{{ $media->fallbackUrl() }}"
            width="{{ $fallbackWidth }}"
            height="{{ $media->heightFor($fallbackWidth) }}"
            alt="{{ $alt ?? $media->alt() }}"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            decoding="async"
            @if ($eager) fetchpriority="high" @endif
            @if ($imgClass) class="{{ $imgClass }}" @endif
        >
    </picture>
@endif
