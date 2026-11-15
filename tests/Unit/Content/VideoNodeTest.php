<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Nodes\VideoNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(VideoNode::class)]
final class VideoNodeTest extends TestCase
{
    /** @return iterable<string, array{string, string|null}> */
    public static function urls(): iterable
    {
        $youtube = 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ';
        $vimeo = 'https://player.vimeo.com/video/76979871';

        yield 'YouTube watch page' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', $youtube];
        yield 'YouTube watch page with more parameters' => ['https://youtube.com/watch?list=x&v=dQw4w9WgXcQ&t=10', $youtube];
        yield 'YouTube short link' => ['https://youtu.be/dQw4w9WgXcQ?t=10', $youtube];
        yield 'YouTube embed' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', $youtube];
        yield 'YouTube without cookies' => [$youtube, $youtube];
        yield 'Vimeo page' => ['https://vimeo.com/76979871', $vimeo];
        yield 'Vimeo player' => [$vimeo . '?h=abc', $vimeo];
        yield 'not https' => ['http://www.youtube.com/watch?v=dQw4w9WgXcQ', null];
        yield 'another site' => ['https://example.com/watch?v=dQw4w9WgXcQ', null];
        yield 'a host that only ends like YouTube' => ['https://evilyoutube.com/watch?v=dQw4w9WgXcQ', null];
        yield 'a short identifier' => ['https://youtu.be/abc', null];
        yield 'a Vimeo name' => ['https://vimeo.com/channels/staffpicks', null];
    }

    #[DataProvider('urls')]
    public function testItEmbedsOnlyYouTubeAndVimeo(string $url, ?string $embed): void
    {
        self::assertSame($embed, VideoNode::embedUrl($url));
    }
}
