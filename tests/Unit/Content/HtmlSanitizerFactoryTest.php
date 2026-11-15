<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\HtmlSanitizerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlSanitizerFactory::class)]
final class HtmlSanitizerFactoryTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function html(): iterable
    {
        yield 'scripts' => ['<p>a<script>alert(1)</script></p>', '<p>a</p>'];
        yield 'event handlers' => ['<p onclick="x()">a</p>', '<p>a</p>'];
        yield 'styles' => ['<style>p{}</style><p>a</p>', '<p>a</p>'];
        yield 'forms' => ['<form action="/x"><input></form>', ''];
        yield 'javascript links' => ['<a href="javascript:alert(1)">a</a>', '<a>a</a>'];
        yield 'safe links' => ['<a href="https://example.com/a">a</a>', '<a href="https://example.com/a">a</a>'];
        yield 'images over https' => ['<img src="https://example.com/a.jpg" alt="A">', '<img src="https://example.com/a.jpg" alt="A" />'];
        yield 'images over http' => ['<img src="http://example.com/a.jpg">', '<img />'];
        yield 'an allowed iframe' => ['<iframe src="https://player.vimeo.com/video/1" allowfullscreen></iframe>', '<iframe src="https://player.vimeo.com/video/1" allowfullscreen></iframe>'];
        yield 'a subdomain of an allowed host' => ['<iframe src="https://a.player.vimeo.com/x"></iframe>', '<iframe src="https://a.player.vimeo.com/x"></iframe>'];
        yield 'another host' => ['<iframe src="https://evil.example/x"></iframe>', '<iframe></iframe>'];
        yield 'a host that only ends like an allowed one' => ['<iframe src="https://evilplayer.vimeo.com.example/x"></iframe>', '<iframe></iframe>'];
        yield 'an iframe over http' => ['<iframe src="http://player.vimeo.com/video/1"></iframe>', '<iframe></iframe>'];
        yield 'an iframe with its own document' => ['<iframe srcdoc="<script>1</script>"></iframe>', '<iframe></iframe>'];
    }

    #[DataProvider('html')]
    public function testItKeepsOnlyWhatIsSafe(string $html, string $expected): void
    {
        self::assertSame($expected, HtmlSanitizerFactory::create(['player.vimeo.com'])->sanitize($html));
    }

    public function testTheHostsCanBeChanged(): void
    {
        $sanitizer = HtmlSanitizerFactory::create(['Example.com']);

        self::assertSame('<iframe src="https://example.com/x"></iframe>', $sanitizer->sanitize('<iframe src="https://example.com/x"></iframe>'));
        self::assertSame('<iframe></iframe>', $sanitizer->sanitize('<iframe src="https://player.vimeo.com/video/1"></iframe>'));
    }

    public function testALongHtmlIsNotCut(): void
    {
        $html = str_repeat('<p>Hello world</p>', 3000);

        self::assertSame($html, HtmlSanitizerFactory::create([])->sanitize($html));
    }
}
