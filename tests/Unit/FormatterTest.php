<?php

namespace Tests\Unit;

use App\Support\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function test_it_escapes_html(): void
    {
        $html = (string) Formatter::render('<script>alert(1)</script> & <b>bold</b>');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_blank_lines_make_paragraphs(): void
    {
        $this->assertSame("<p>One</p>\n<p>Two<br>\nstill two</p>", (string) Formatter::render("One\n\nTwo\nstill two"));
    }

    public function test_asterisks_make_italics(): void
    {
        $this->assertSame('<p>a <i>very</i> good point, 2*3*4</p>', (string) Formatter::render('a *very* good point, 2*3*4'));
    }

    public function test_indented_blocks_become_code(): void
    {
        $this->assertSame('<pre><code>if ($a &lt; $b) {'."\n".'  return;</code></pre>', (string) Formatter::render("  if (\$a < \$b) {\n    return;"));
    }

    public function test_links_are_clickable_without_swallowing_punctuation(): void
    {
        $html = (string) Formatter::render('See https://example.com/a?b=1&c=2.');

        $this->assertSame('<p>See <a href="https://example.com/a?b=1&amp;c=2" rel="nofollow ugc noopener">https://example.com/a?b=1&amp;c=2</a>.</p>', $html);
    }

    public function test_javascript_urls_are_not_linked(): void
    {
        $this->assertStringNotContainsString('<a', (string) Formatter::render('javascript:alert(1)'));
    }
}
