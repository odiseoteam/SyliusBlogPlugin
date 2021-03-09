<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture\Factory;

use Faker\Factory;
use Odiseo\BlogBundle\Model\ImageInterface;
use Odiseo\BlogBundle\Uploader\ImageUploaderInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCommentInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlogArticleExampleFactory extends AbstractExampleFactory
{
    /** @var FactoryInterface */
    private $articleFactory;

    /** @var FactoryInterface */
    private $articleImageFactory;

    /** @var FactoryInterface */
    private $articleCommentFactory;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $articleCategoryRepository;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var ImageUploaderInterface */
    private $imageUploader;

    /** @var FileLocatorInterface|null */
    private $fileLocator;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        FactoryInterface $articleFactory,
        FactoryInterface $articleImageFactory,
        FactoryInterface $articleCommentFactory,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $articleCategoryRepository,
        RepositoryInterface $localeRepository,
        ImageUploaderInterface $imageUploader,
        ?FileLocatorInterface $fileLocator = null
    ) {
        $this->articleFactory = $articleFactory;
        $this->articleImageFactory = $articleImageFactory;
        $this->articleCommentFactory = $articleCommentFactory;
        $this->channelRepository = $channelRepository;
        $this->articleCategoryRepository = $articleCategoryRepository;
        $this->localeRepository = $localeRepository;
        $this->imageUploader = $imageUploader;
        $this->fileLocator = $fileLocator;

        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ArticleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ArticleInterface $article */
        $article = $this->articleFactory->createNew();
        $article->setCode($options['code']);
        $article->setEnabled($options['enabled']);

        foreach ($options['channels'] as $channel) {
            $article->addChannel($channel);
        }

        foreach ($options['categories'] as $category) {
            $article->addCategory($category);
        }

        /** @var string $localeCode */
        foreach ($this->getLocales() as $localeCode) {
            $article->setCurrentLocale($localeCode);
            $article->setFallbackLocale($localeCode);

            $article->setTitle($options['title']);
            $article->setContent($options['content']);
            $article->setSlug($options['slug']);
        }

        $this->createImages($article, $options);

        if (rand(0, 100) > 50) {
            $this->addArticleComments($article);
        }

        return $article;
    }

    private function createImages(ArticleInterface $article, array $options): void
    {
        foreach ($options['images'] as $imagePath) {
            $imagePath = null === $this->fileLocator ? $imagePath : $this->fileLocator->locate($imagePath);
            $uploadedImage = new UploadedFile($imagePath, basename($imagePath));

            /** @var ImageInterface $articleImage */
            $articleImage = $this->articleImageFactory->createNew();
            $articleImage->setFile($uploadedImage);

            $this->imageUploader->upload($articleImage);

            $article->addImage($articleImage);
        }
    }

    private function addArticleComments(ArticleInterface $article): void
    {
        for ($i = 0; $i < 8; ++$i) {
            /** @var ArticleCommentInterface $comment */
            $comment = $this->articleCommentFactory->createNew();
            $comment->setName($this->faker->name);
            $comment->setEmail($this->faker->email);
            $comment->setComment($this->faker->text);
            $comment->setEnabled($this->faker->boolean(70));

            $article->addComment($comment);
        }
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
            ->setDefault('channels', LazyOption::randomOnes($this->channelRepository, 3))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setDefault('categories', LazyOption::randomOnes($this->articleCategoryRepository, 1))
            ->setAllowedTypes('categories', 'array')
            ->setNormalizer('categories', LazyOption::findBy($this->articleCategoryRepository, 'code'))
            ->setDefault('title', function (Options $options): string {
                return $this->faker->text(20);
            })
            ->setDefault('content', function (Options $options): string {
                return $this->faker->text;
            })
            ->setDefault('slug', function (Options $options): string {
                return StringInflector::nameToCode((string) $options['title']);
            })
            ->setDefault('images', function (Options $options): array {
                return [
                    __DIR__.'/../../Resources/fixtures/article/images/0'.rand(1, 6).'.jpg',
                ];
            })
            ->setAllowedTypes('images', 'array')
        ;
    }
}
