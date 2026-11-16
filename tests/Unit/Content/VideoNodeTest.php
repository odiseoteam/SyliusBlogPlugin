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
        // Shared with the editor, so both accept the same videos.
        /** @var list<array{case: string, url: string, embed: string|null}> $cases */
        $cases = json_decode((string) file_get_contents(__DIR__ . '/../../Fixtures/Content/videos.json'), true);
        foreach ($cases as $case) {
            yield $case['case'] => [$case['url'], $case['embed']];
        }
    }

    #[DataProvider('urls')]
    public function testItEmbedsOnlyYouTubeAndVimeo(string $url, ?string $embed): void
    {
        self::assertSame($embed, VideoNode::embedUrl($url));
    }
}
