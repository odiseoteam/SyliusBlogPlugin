<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Extractor\EmbeddedProductsExtractor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmbeddedProductsExtractor::class)]
final class EmbeddedProductsExtractorTest extends TestCase
{
    public function testItListsTheProductsInOrderWithoutRepeats(): void
    {
        $pipeline = new ContentPipeline();
        $document = $pipeline->parser->parse(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'product', 'attrs' => ['code' => 'MUG']],
            ['type' => 'blockquote', 'content' => [['type' => 'product', 'attrs' => ['code' => 'BEANS']]]],
            ['type' => 'product', 'attrs' => ['code' => 'MUG']],
            ['type' => 'product', 'attrs' => ['code' => 'not a code']],
            ['type' => 'product'],
            ['type' => 'mystery', 'content' => [['type' => 'product', 'attrs' => ['code' => 'HIDDEN']]]],
        ]], \JSON_THROW_ON_ERROR));

        self::assertSame(['MUG', 'BEANS'], (new EmbeddedProductsExtractor($pipeline->registry))->extract($document));
    }
}
