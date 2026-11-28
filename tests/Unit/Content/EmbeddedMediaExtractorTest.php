<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Extractor\EmbeddedMediaExtractor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmbeddedMediaExtractor::class)]
final class EmbeddedMediaExtractorTest extends TestCase
{
    public function testItListsTheFilesOfImagesAndGalleriesInOrderWithoutRepeats(): void
    {
        $pipeline = new ContentPipeline();
        $document = $pipeline->parser->parse(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'image', 'attrs' => ['path' => 'ab/cd/mugs.jpg']],
            ['type' => 'gallery', 'attrs' => ['images' => [['path' => 'ab/cd/one.jpg'], ['path' => '../two.jpg'], ['path' => 'ab/cd/mugs.jpg']]]],
            ['type' => 'blockquote', 'content' => [['type' => 'image', 'attrs' => ['path' => 'ab/cd/quoted.png']]]],
            ['type' => 'image'],
            ['type' => 'mystery', 'content' => [['type' => 'image', 'attrs' => ['path' => 'ab/cd/hidden.jpg']]]],
        ]], \JSON_THROW_ON_ERROR));

        self::assertSame(
            ['ab/cd/mugs.jpg', 'ab/cd/one.jpg', 'ab/cd/quoted.png'],
            (new EmbeddedMediaExtractor($pipeline->registry))->extract($document),
        );
    }
}
