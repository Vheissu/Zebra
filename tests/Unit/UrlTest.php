<?php

namespace Tests\Unit;

use App\Support\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_domain_drops_www_and_lowercases(): void
    {
        $this->assertSame('example.com', Url::domain('https://WWW.Example.com/path'));
        $this->assertSame('blog.example.com', Url::domain('http://blog.example.com'));
    }

    public function test_key_treats_trivially_different_urls_as_the_same(): void
    {
        $key = Url::key('https://example.com/post');

        $this->assertSame($key, Url::key('http://www.example.com/post/'));
        $this->assertSame($key, Url::key('https://example.com/post#comments'));
        $this->assertSame($key, Url::key('https://example.com/post?utm_source=hn&ref=twitter'));
        $this->assertNotSame($key, Url::key('https://example.com/post?page=2'));
    }

    public function test_key_ignores_query_parameter_order(): void
    {
        $this->assertSame(Url::key('https://a.com/?x=1&y=2'), Url::key('https://a.com/?y=2&x=1'));
    }
}
