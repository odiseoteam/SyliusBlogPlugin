<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Extractor\PlainTextExtractor;
use Odiseo\SyliusBlogPlugin\Content\Extractor\WordCounter;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTranslationInterface;

/** Keeps the plain text and the word count of a translation in step with its content. */
final class ArticleTranslationContentListener
{
    public function __construct(
        private readonly DocumentParser $parser,
        private readonly PlainTextExtractor $plainText,
        private readonly WordCounter $wordCounter,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = $args->getObjectManager();
        $unitOfWork = $manager->getUnitOfWork();

        foreach ([...$unitOfWork->getScheduledEntityInsertions(), ...$unitOfWork->getScheduledEntityUpdates()] as $translation) {
            if ($translation instanceof ArticleTranslationInterface && array_key_exists('content', $unitOfWork->getEntityChangeSet($translation))) {
                $this->derive($translation);
                $unitOfWork->recomputeSingleEntityChangeSet($manager->getClassMetadata($translation::class), $translation);
            }
        }
    }

    private function derive(ArticleTranslationInterface $translation): void
    {
        $text = $this->plainText->extract($this->parser->parse($translation->getContent()));

        $translation->setPlainText('' === $text ? null : $text);
        $translation->setWordCount($this->wordCounter->count($text));
    }
}
