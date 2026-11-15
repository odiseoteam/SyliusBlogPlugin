<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Marks\BoldMark;
use Odiseo\SyliusBlogPlugin\Content\Marks\ItalicMark;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ParagraphNode;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentRegistry::class)]
final class ContentRegistryTest extends TestCase
{
    public function testItFindsNodesAndMarksByType(): void
    {
        $registry = new ContentRegistry([new ParagraphNode()], [new BoldMark()]);

        self::assertInstanceOf(ParagraphNode::class, $registry->getNode('paragraph'));
        self::assertInstanceOf(BoldMark::class, $registry->getMark('bold'));
        self::assertNull($registry->getNode('mystery'));
        self::assertNull($registry->getMark('italic'));
    }

    public function testTheFirstOfARepeatedTypeWins(): void
    {
        $first = new ParagraphNode();
        $registry = new ContentRegistry([$first, new ParagraphNode()], [new BoldMark(), new ItalicMark()]);

        self::assertSame($first, $registry->getNode('paragraph'));
    }
}
