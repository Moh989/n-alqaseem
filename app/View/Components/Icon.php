<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Inline Lucide icon (ISC licence) from resources/icons. Decorative by default.
 */
class Icon extends Component
{
    /**
     * @var array<string, string>
     */
    protected static array $cache = [];

    public function __construct(public string $name, public ?string $label = null) {}

    public function svg(): string
    {
        $name = preg_replace('/[^a-z0-9-]/', '', $this->name);

        return static::$cache[$name] ??= is_file($path = resource_path("icons/{$name}.svg"))
            ? trim((string) file_get_contents($path))
            : '';
    }

    public function render(): string
    {
        return <<<'BLADE'
            @php($svg = $svg())
            @if ($svg !== '')
                {!! str_replace('<svg ', '<svg '.$attributes->class(['icon', 'icon--'.$name])->merge($label ? ['role' => 'img', 'aria-label' => $label] : ['aria-hidden' => 'true', 'focusable' => 'false'])->toHtml().' ', $svg) !!}
            @endif
            BLADE;
    }
}
