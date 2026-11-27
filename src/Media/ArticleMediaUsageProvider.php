<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Media;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Extractor\EmbeddedMediaExtractor;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTranslationInterface;

/** The files shown in the content of the articles, in every language. */
final class ArticleMediaUsageProvider implements MediaUsageProviderInterface
{
    /** @param class-string<ArticleTranslationInterface> $translationClass */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly DocumentParser $parser,
        private readonly EmbeddedMediaExtractor $extractor,
        private readonly string $translationClass,
    ) {
    }

    public function getUsedPaths(): iterable
    {
        /** @var iterable<array{content: string|null}> $rows */
        $rows = $this->manager->createQueryBuilder()
            ->select('translation.content')
            ->from($this->translationClass, 'translation')
            ->where('translation.content IS NOT NULL')
            ->getQuery()
            ->toIterable()
        ;

        foreach ($rows as $row) {
            yield from $this->extractor->extract($this->parser->parse($row['content']));
        }
    }
}
