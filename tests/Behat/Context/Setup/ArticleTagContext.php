<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ArticleTagContext implements Context
{
    /** @param FactoryInterface<ArticleTagInterface> $tagFactory */
    public function __construct(
        private readonly FactoryInterface $tagFactory,
        private readonly ObjectManager $manager,
        private readonly SlugGeneratorInterface $slugGenerator,
    ) {
    }

    #[Given('the blog has a tag :name')]
    public function theBlogHasATag(string $name): void
    {
        $tag = $this->tagFactory->createNew();
        $tag->setCode(strtoupper(str_replace('-', '_', $this->slugGenerator->generate($name))));
        $tag->setName($name);
        $tag->setSlug($this->slugGenerator->generate($name));

        $this->manager->persist($tag);
        $this->manager->flush();
    }
}
