<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Turns the plain text people type into safe HTML, with the same small set
 * of rules Hacker News uses:
 *
 *  - blank lines separate paragraphs
 *  - lines indented by two or more spaces are shown as code
 *  - *text* is shown in italics
 *  - bare http(s) links become clickable
 *
 * Everything is escaped first, so no markup a user types ever reaches the page.
 */
class Formatter
{
    public static function render(?string $text): HtmlString
    {
        $text = str_replace(["\r\n", "\r"], "\n", rtrim((string) $text));
        $text = preg_replace('/^(?:[ \t]*\n)+/', '', $text);

        if ($text === '') {
            return new HtmlString('');
        }

        $blocks = preg_split('/\n\s*\n/', $text);

        $html = array_map(function (string $block) {
            $lines = explode("\n", $block);

            if (collect($lines)->every(fn ($line) => str_starts_with($line, '  '))) {
                return '<pre><code>'.e(implode("\n", array_map(fn ($l) => substr($l, 2), $lines))).'</code></pre>';
            }

            return '<p>'.static::inline($block).'</p>';
        }, $blocks);

        return new HtmlString(implode("\n", $html));
    }

    private static function inline(string $text): string
    {
        $html = nl2br(e($text), false);

        $html = preg_replace('/(?<![\w*])\*(?=\S)([^*\n]+?)(?<=\S)\*(?![\w*])/u', '<i>$1</i>', $html);

        return preg_replace_callback(
            '~\bhttps?://[^\s<>"\']+~i',
            function (array $match) {
                // Leave trailing punctuation outside the link: "see https://example.com."
                $url = rtrim($match[0], '.,;:!?)');
                $trail = substr($match[0], strlen($url));

                return '<a href="'.$url.'" rel="nofollow ugc noopener">'.$url.'</a>'.$trail;
            },
            $html,
        );
    }
}
