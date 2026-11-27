<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Product;

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
        $pipeline = new ContentPipeline(products: ['COFFEE_MUG' => self::mug()]);
        $document = $pipeline->parser->parse((string) file_get_contents($snapshot . '.json'));

        self::assertStringEqualsFile($snapshot . '.html', $pipeline->html->render($document) . "\n");
        self::assertStringEqualsFile($snapshot . '.md', $pipeline->markdown->render($document) . "\n");
        self::assertStringEqualsFile($snapshot . '.txt', $pipeline->plainText->extract($document) . "\n");
    }

    private static function mug(): Product
    {
        $product = new Product();
        $product->setCode('COFFEE_MUG');
        $product->setCurrentLocale('en_US');
        $product->setFallbackLocale('en_US');
        $product->setName('Coffee [mug]');
        $product->setSlug('coffee-mug');

        return $product;
    }
}
