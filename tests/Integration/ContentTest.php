<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Extractor\TocExtractor;
use Odiseo\SyliusBlogPlugin\Content\Renderer\HtmlRenderer;
use Odiseo\SyliusBlogPlugin\Content\Renderer\MarkdownRenderer;
use Odiseo\SyliusBlogPlugin\Content\Validator\ContentValidator;
use Odiseo\SyliusBlogPlugin\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ContentTest extends KernelTestCase
{
    private const CONTENT = '{"type":"doc","content":[{"type":"heading","attrs":{"level":2},"content":[{"type":"text","text":"Brewing"}]},{"type":"paragraph","content":[{"type":"text","text":"Hi","marks":[{"type":"bold"}]}]},{"type":"video","attrs":{"src":"https://youtu.be/dQw4w9WgXcQ"}}]}';

    public function testTheContentIsDrawnWithTheTemplatesOfThePlugin(): void
    {
        $document = $this->service('odiseo_blog.content.parser', DocumentParser::class)->parse(self::CONTENT);

        self::assertSame(
            '<h2 id="brewing">Brewing</h2><p><strong>Hi</strong></p><div class="ratio ratio-16x9"><iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ" title="Video" loading="lazy" allowfullscreen></iframe></div>',
            $this->service('odiseo_blog.content.renderer.html', HtmlRenderer::class)->render($document),
        );
        self::assertSame(
            "## Brewing\n\n**Hi**\n\n<https://youtu.be/dQw4w9WgXcQ>",
            $this->service('odiseo_blog.content.renderer.markdown', MarkdownRenderer::class)->render($document),
        );
        self::assertSame(
            ['brewing'],
            array_map(static fn ($entry): string => $entry->id, $this->service('odiseo_blog.content.extractor.toc', TocExtractor::class)->extract($document)),
        );
    }

    public function testTheValidatorRejectsWhatTheBlogDoesNotKnowAndSaysWhy(): void
    {
        $validator = $this->service('odiseo_blog.content.validator', ContentValidator::class);

        self::assertSame([], $validator->validate(self::CONTENT));
        self::assertSame('odiseo_blog.content.unknown_node', $validator->validate('{"type":"doc","content":[{"type":"mystery"}]}')[0]->message);
        $translator = self::getContainer()->get('translator');
        self::assertInstanceOf(TranslatorInterface::class, $translator);
        self::assertSame(
            'The "level" of "heading" is not valid: The value you selected is not a valid choice.',
            $translator->trans(
                'odiseo_blog.content.invalid_attribute',
                $validator->validate('{"type":"doc","content":[{"type":"heading","attrs":{"level":1}}]}')[0]->parameters,
                'validators',
                'en',
            ),
        );
    }

    public function testTemplatesGetTheReadingTimeOfAnArticle(): void
    {
        $article = new Article();
        $article->setCurrentLocale('en_US');
        $article->setFallbackLocale('en_US');
        $article->getTranslation()->setWordCount(401);

        $twig = $this->service('twig', Environment::class);

        self::assertSame('3', $twig->createTemplate('{{ odiseo_blog_reading_time(article) }}')->render(['article' => $article]));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    private function service(string $id, string $class): object
    {
        self::bootKernel();

        $service = self::getContainer()->get($id);
        self::assertInstanceOf($class, $service);

        return $service;
    }
}
