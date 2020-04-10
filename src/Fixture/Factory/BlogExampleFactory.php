<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Generator;
use Odiseo\BlogBundle\EventListener\ArticleImageUploadListener;
use Odiseo\BlogBundle\Model\ArticleCategoryInterface;
use Odiseo\BlogBundle\Model\ArticleImageInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCommentInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlogExampleFactory extends AbstractExampleFactory
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var FactoryInterface */
    private $articleFactory;

    /** @var FactoryInterface */
    private $articleCategoryFactory;

    /** @var FactoryInterface */
    private $articleImageFactory;

    /** @var FactoryInterface */
    private $articleCommentFactory;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var ArticleImageUploadListener */
    private $imageUploader;

    /** @var FileLocatorInterface|null */
    private $fileLocator;

    /** @var OptionsResolver */
    private $optionsResolver;

    /** @var \Faker\Generator */
    private $faker;

    public function __construct(
        ObjectManager $objectManager,
        FactoryInterface $articleFactory,
        FactoryInterface $articleCategoryFactory,
        FactoryInterface $articleImageFactory,
        FactoryInterface $articleCommentFactory,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository,
        ArticleImageUploadListener $imageUploader,
        ?FileLocatorInterface $fileLocator = null
    ) {
        $this->objectManager = $objectManager;
        $this->articleFactory = $articleFactory;
        $this->articleCategoryFactory = $articleCategoryFactory;
        $this->articleImageFactory = $articleImageFactory;
        $this->articleCommentFactory = $articleCommentFactory;
        $this->channelRepository = $channelRepository;
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
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('channels', LazyOption::randomOnes($this->channelRepository, 3))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))

            ->setDefault('image', function (Options $options): string {
                return __DIR__.'/../../Resources/fixtures/article/0'.rand(1, 4).'.png';
            })
            ->setAllowedTypes('image', ['string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ArticleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ArticleInterface $article */
        $article = $this->articleFactory->createNew();
        $article->setCode($this->faker->md5);
        $article->setEnabled(rand(1, 100) > 30);

        foreach ($options['channels'] as $channel) {
            $article->addChannel($channel);
        }

        $this->createArticleCategory($article);

        /** @var string $localeCode */
        foreach ($this->getLocales() as $localeCode) {
            $article->setCurrentLocale($localeCode);
            $article->setFallbackLocale($localeCode);

            $article->getTranslation()->setTitle($this->faker->text(20));
            $article->getTranslation()->setContent($this->faker->text(400));
            $article->getTranslation()->setSlug($this->faker->slug);
        }

        /** @var ArticleImageInterface $image */
        $image = $this->articleImageFactory->createNew();
        $image->setFile($this->createImage($options['image']));

        $article->addImage($image);

        $this->imageUploader->uploadImages(new ResourceControllerEvent($article));

        if (rand(0, 100) > 50) {
            $this->addArticleComments($article);
        }

        return $article;
    }

    /**
     * @param ArticleInterface $article
     */
    private function createArticleCategory(ArticleInterface $article): void
    {
        /** @var ArticleCategoryInterface $articleCategory */
        $articleCategory = $this->articleCategoryFactory->createNew();
        $articleCategory->setCode($this->faker->md5);
        $articleCategory->setEnabled(true);

        foreach ($this->getLocales() as $localeCode) {
            $articleCategory->setCurrentLocale($localeCode);
            $articleCategory->setFallbackLocale($localeCode);

            $articleCategory->getTranslation()->setTitle($this->faker->text(20));
            $articleCategory->getTranslation()->setSlug($this->faker->slug);
        }

        $article->addCategory($articleCategory);

        $this->objectManager->persist($articleCategory);
    }

    /**
     * @param ArticleInterface $article
     */
    private function addArticleComments(ArticleInterface $article): void
    {
        for ($i = 0; $i < 8; $i++) {
            /** @var ArticleCommentInterface $comment */
            $comment = $this->articleCommentFactory->createNew();
            $comment->setName($this->faker->name);
            $comment->setEmail($this->faker->email);
            $comment->setComment($this->faker->text);
            $comment->setEnabled(rand(1, 100) > 30);

            $article->addComment($comment);

            $this->objectManager->persist($comment);
        }
    }

    /**
     * @param string $imagePath
     * @return UploadedFile
     */
    private function createImage(string $imagePath): UploadedFile
    {
        $imagePath = $this->fileLocator === null ? $imagePath : $this->fileLocator->locate($imagePath);
        if(is_array($imagePath) && count($imagePath) > 0)
            $imagePath = $imagePath[0];

        return new UploadedFile($imagePath, basename($imagePath));
    }

    /**
     * @return Generator
     */
    private function getLocales(): Generator
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
