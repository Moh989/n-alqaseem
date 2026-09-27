<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Converts admin-entered plain text into safe HTML.
 *
 * Everything is escaped first. Then: blank lines separate paragraphs, lines starting
 * with "- " become list items, and lines starting with "## " become headings.
 */
class TextFormatter
{
    public static function toHtml(?string $text, int $headingLevel = 2): HtmlString
    {
        if (blank($text)) {
            return new HtmlString('');
        }

        $text = str_replace(["\r\n", "\r"], "\n", trim($text));
        $blocks = preg_split("/\n{2,}/", $text) ?: [];
        $html = [];

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $block)), fn (string $line): bool => $line !== ''));
            $paragraph = [];
            $list = [];

            $flushParagraph = function () use (&$paragraph, &$html): void {
                if ($paragraph !== []) {
                    $html[] = '<p>'.implode('<br>', $paragraph).'</p>';
                    $paragraph = [];
                }
            };
            $flushList = function () use (&$list, &$html): void {
                if ($list !== []) {
                    $html[] = '<ul>'.implode('', array_map(fn (string $item): string => '<li>'.$item.'</li>', $list)).'</ul>';
                    $list = [];
                }
            };

            foreach ($lines as $line) {
                if (str_starts_with($line, '## ')) {
                    $flushParagraph();
                    $flushList();
                    $html[] = sprintf('<h%1$d>%2$s</h%1$d>', $headingLevel, e(trim(substr($line, 3))));
                } elseif (str_starts_with($line, '- ')) {
                    $flushParagraph();
                    $list[] = e(trim(substr($line, 2)));
                } else {
                    $flushList();
                    $paragraph[] = e($line);
                }
            }

            $flushParagraph();
            $flushList();
        }

        return new HtmlString(implode("\n", $html));
    }

    /**
     * Plain-text excerpt for meta descriptions.
     */
    public static function excerpt(?string $text, int $limit = 160): string
    {
        $plain = preg_replace('/^(## |- )/m', '', (string) $text);
        $plain = trim(preg_replace('/\s+/u', ' ', (string) $plain));

        return mb_strimwidth($plain, 0, $limit, '…');
    }
}
