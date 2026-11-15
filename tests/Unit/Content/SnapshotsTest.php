<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** Each `Snapshots/<name>.json` has the HTML, the Markdown and the plain text it must produce. */
final class SnapshotsTest extends TestCase
{
    /** @return iterable<string, array{string}> */
    public static function snapshots(): iterable
    {
        foreach (glob(__DIR__ . '/Snapshots/*.json') ?: [] as $file) {
            yield basename($file, '.json') => [substr($file, 0, -5)];
        }
    }

    #[DataProvider('snapshots')]
    public function testTheContentIsDrawnAsInTheSnapshot(string $snapshot): void
    {
        $pipeline = new ContentPipeline();
        $document = $pipeline->parser->parse((string) file_get_contents($snapshot . '.json'));

        self::assertStringEqualsFile($snapshot . '.html', $pipeline->html->render($document) . "\n");
        self::assertStringEqualsFile($snapshot . '.md', $pipeline->markdown->render($document) . "\n");
        self::assertStringEqualsFile($snapshot . '.txt', $pipeline->plainText->extract($document) . "\n");
    }
}
