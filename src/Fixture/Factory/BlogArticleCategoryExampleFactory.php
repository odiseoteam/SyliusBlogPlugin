<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture\Factory;

use Faker\Factory;
use Odiseo\BlogBundle\Model\ArticleCategoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlogArticleCategoryExampleFactory extends AbstractExampleFactory
{
    /** @var FactoryInterface */
    private $articleCategoryFactory;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        FactoryInterface $articleCategoryFactory,
        RepositoryInterface $localeRepository
    ) {
        $this->articleCategoryFactory = $articleCategoryFactory;
        $this->localeRepository = $localeRepository;

        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ArticleCategoryInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ArticleCategoryInterface $articleCategory */
        $articleCategory = $this->articleCategoryFactory->createNew();
        $articleCategory->setCode($options['code']);
        $articleCategory->setEnabled($options['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $articleCategory->setCurrentLocale((string) $localeCode);
            $articleCategory->setFallbackLocale((string) $localeCode);

            $articleCategory->setTitle($options['title']);
            $articleCategory->setSlug($options['slug']);
        }

        return $articleCategory;
    }

    private function getLocales(): \Generator
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode((string) $options['title']);
            })
            ->setDefault('enabled', function (Options $options): bool {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('title', function (Options $options): string {
                return $this->faker->words(3, true);
            })
            ->setDefault('slug', function (Options $options): string {
                return StringInflector::nameToCode((string) $options['title']);
            })
        ;
    }
}
