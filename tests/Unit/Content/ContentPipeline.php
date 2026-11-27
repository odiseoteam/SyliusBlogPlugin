<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Extractor\PlainTextExtractor;
use Odiseo\SyliusBlogPlugin\Content\Extractor\TocExtractor;
use Odiseo\SyliusBlogPlugin\Content\HtmlSanitizerFactory;
use Odiseo\SyliusBlogPlugin\Content\Marks;
use Odiseo\SyliusBlogPlugin\Content\Nodes;
use Odiseo\SyliusBlogPlugin\Content\Product\ProductFinderInterface;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use Odiseo\SyliusBlogPlugin\Content\Renderer\HtmlRenderer;
use Odiseo\SyliusBlogPlugin\Content\Renderer\MarkdownRenderer;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/** The content pipeline with every node and mark of the plugin, without the container. */
final class ContentPipeline
{
    public readonly ContentRegistry $registry;

    public readonly DocumentParser $parser;

    public readonly PlainTextExtractor $plainText;

    public readonly TocExtractor $toc;

    public readonly HtmlRenderer $html;

    public readonly MarkdownRenderer $markdown;

    /**
     * @param list<string> $iframeHosts
     * @param array<string, ProductInterface> $products The products of the channel, by code.
     */
    public function __construct(array $iframeHosts = ['www.youtube-nocookie.com', 'player.vimeo.com'], array $products = [])
    {
        $productFinder = new class($products) implements ProductFinderInterface {
            /** @param array<string, ProductInterface> $products */
            public function __construct(private readonly array $products)
            {
            }

            public function find(string $code): ?ProductInterface
            {
                return $this->products[$code] ?? null;
            }

            public function preload(array $codes): void
            {
            }
        };
        $routes = new RouteCollection();
        $routes->add('sylius_shop_product_show', new Route('/products/{slug}'));

        $this->registry = new ContentRegistry(
            [
                new Nodes\ParagraphNode(), new Nodes\HeadingNode(), new Nodes\BlockquoteNode(), new Nodes\BulletListNode(),
                new Nodes\OrderedListNode(), new Nodes\ListItemNode(), new Nodes\CodeBlockNode(), new Nodes\HorizontalRuleNode(),
                new Nodes\HardBreakNode(), new Nodes\TableNode(), new Nodes\TableRowNode(), new Nodes\TableHeaderNode(),
                new Nodes\TableCellNode(), new Nodes\VideoNode(), new Nodes\HtmlNode(HtmlSanitizerFactory::create($iframeHosts)),
                new Nodes\ProductNode($productFinder, new UrlGenerator($routes, new RequestContext(host: 'shop.example.com', scheme: 'https'))),
            ],
            [
                new Marks\BoldMark(), new Marks\ItalicMark(), new Marks\UnderlineMark(),
                new Marks\StrikeMark(), new Marks\CodeMark(), new Marks\LinkMark(),
            ],
        );

        $loader = new FilesystemLoader();
        $loader->addPath(__DIR__ . '/../../../templates', 'OdiseoSyliusBlogPlugin');
        $twig = new Environment($loader, ['strict_variables' => true]);
        $twig->addFilter(new TwigFilter('trans', static fn (string $key): string => $key));
        // The product card of the shop, reduced to the code of its product.
        $twig->addFunction(new TwigFunction(
            'component',
            static fn (string $name, array $props): string => sprintf('<%s product="%s"></%1$s>', str_replace(':', '-', $name), $props['product'] instanceof ProductInterface ? $props['product']->getCode() : ''),
            ['is_safe' => ['html']],
        ));

        $this->parser = new DocumentParser();
        $this->plainText = new PlainTextExtractor($this->registry);
        $this->toc = new TocExtractor($this->registry, $this->plainText);
        $this->html = new HtmlRenderer($this->registry, $this->toc, $twig);
        $this->markdown = new MarkdownRenderer($this->registry);
    }
}
