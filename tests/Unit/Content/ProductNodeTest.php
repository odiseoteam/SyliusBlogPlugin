<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ParagraphNode;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ProductNode;
use Odiseo\SyliusBlogPlugin\Content\Product\ProductFinderInterface;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(ProductNode::class)]
#[CoversClass(ContentRegistry::class)]
final class ProductNodeTest extends TestCase
{
    public function testTheProductsOfADocumentAreLoadedAtOnceBeforeItIsDrawn(): void
    {
        $finder = new class() implements ProductFinderInterface {
            /** @var list<list<string>> */
            public array $preloaded = [];

            public function find(string $code): ?ProductInterface
            {
                return null;
            }

            public function preload(array $codes): void
            {
                $this->preloaded[] = $codes;
            }
        };
        $registry = new ContentRegistry([new ParagraphNode(), new ProductNode($finder, $this->createStub(UrlGeneratorInterface::class))], []);

        $registry->preload((new DocumentParser())->parse(json_encode(['type' => 'doc', 'content' => [
            ['type' => 'product', 'attrs' => ['code' => 'MUG']],
            ['type' => 'blockquote', 'content' => [['type' => 'product', 'attrs' => ['code' => 'BEANS']]]],
            ['type' => 'product', 'attrs' => ['code' => 'MUG']],
            ['type' => 'product', 'attrs' => ['code' => '../etc']],
        ]], \JSON_THROW_ON_ERROR)));
        $registry->preload((new DocumentParser())->parse('{"type":"doc","content":[{"type":"paragraph"}]}'));

        self::assertSame([['MUG', 'BEANS']], $finder->preloaded);
    }
}
