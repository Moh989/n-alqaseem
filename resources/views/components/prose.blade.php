@props(['text', 'headingLevel' => 2])
<div {{ $attributes->class(['prose']) }}>{{ \App\Support\TextFormatter::toHtml($text, $headingLevel) }}</div>
