<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Extractor\PlainTextExtractor;
use Odiseo\SyliusBlogPlugin\Content\Extractor\TocExtractor;
use Odiseo\SyliusBlogPlugin\Content\HtmlSanitizerFactory;
use Odiseo\SyliusBlogPlugin\Content\Marks;
use Odiseo\SyliusBlogPlugin\Content\Nodes;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use Odiseo\SyliusBlogPlugin\Content\Renderer\HtmlRenderer;
use Odiseo\SyliusBlogPlugin\Content\Renderer\MarkdownRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/** The content pipeline with every node and mark of the plugin, without the container. */
final class ContentPipeline
{
    public readonly ContentRegistry $registry;

    public readonly DocumentParser $parser;

    public readonly PlainTextExtractor $plainText;

    public readonly TocExtractor $toc;

    public readonly HtmlRenderer $html;

    public readonly MarkdownRenderer $markdown;

    /** @param list<string> $iframeHosts */
    public function __construct(array $iframeHosts = ['www.youtube-nocookie.com', 'player.vimeo.com'])
    {
        $this->registry = new ContentRegistry(
            [
                new Nodes\ParagraphNode(), new Nodes\HeadingNode(), new Nodes\BlockquoteNode(), new Nodes\BulletListNode(),
                new Nodes\OrderedListNode(), new Nodes\ListItemNode(), new Nodes\CodeBlockNode(), new Nodes\HorizontalRuleNode(),
                new Nodes\HardBreakNode(), new Nodes\TableNode(), new Nodes\TableRowNode(), new Nodes\TableHeaderNode(),
                new Nodes\TableCellNode(), new Nodes\VideoNode(), new Nodes\HtmlNode(HtmlSanitizerFactory::create($iframeHosts)),
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

        $this->parser = new DocumentParser();
        $this->plainText = new PlainTextExtractor($this->registry);
        $this->toc = new TocExtractor($this->registry, $this->plainText);
        $this->html = new HtmlRenderer($this->registry, $this->toc, $twig);
        $this->markdown = new MarkdownRenderer($this->registry);
    }
}
