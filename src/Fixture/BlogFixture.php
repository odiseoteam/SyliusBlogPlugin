<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Odiseo\BlogBundle\EventListener\ArticleImageUploadListener;
use Odiseo\BlogBundle\Model\ArticleCategoryInterface;
use Odiseo\BlogBundle\Model\ArticleImageInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCommentInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlogFixture extends AbstractFixture
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

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    /** @var ArticleImageUploadListener */
    private $imageUploader;

    /** @var array */
    private $categories;

    public function __construct(
        ObjectManager $objectManager,
        FactoryInterface $articleFactory,
        FactoryInterface $articleCategoryFactory,
        FactoryInterface $articleImageFactory,
        FactoryInterface $articleCommentFactory,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository,
        ArticleImageUploadListener $imageUploader
    ) {
        $this->objectManager = $objectManager;
        $this->articleFactory = $articleFactory;
        $this->articleCategoryFactory = $articleCategoryFactory;
        $this->articleImageFactory = $articleImageFactory;
        $this->articleCommentFactory = $articleCommentFactory;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
        $this->imageUploader = $imageUploader;

        $this->faker = Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('articles_per_channel')
                ->setAllowedTypes('articles_per_channel', 'int')
        ;
    }

    /**
     * @inheritDoc
     */
    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $channels = $this->channelRepository->findAll();

        $this->createArticleCategories();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            for ($i=1; $i <= $options['articles_per_channel']; $i++) {
                /** @var ArticleInterface $article */
                $article = $this->articleFactory->createNew();

                $article->addChannel($channel);
                $article->setCode($this->faker->md5);
                $article->setEnabled(rand(1, 100) > 30);

                // Add categories
                $categories = $this->faker->randomElements($this->categories, rand(1, count($this->categories)));

                /** @var ArticleCategoryInterface $category */
                foreach ($categories as $category) {
                    $article->addCategory($category);
                }

                // Set translatable fields
                foreach ($this->getLocales() as $localeCode) {
                    $article->setCurrentLocale($localeCode);
                    $article->setFallbackLocale($localeCode);

                    $article->getTranslation()->setTitle($this->faker->text(20));
                    $article->getTranslation()->setContent($this->faker->text);
                    $article->getTranslation()->setSlug($this->faker->slug);
                }

                // Add images
                $srcImage = new UploadedFile($this->faker->image(null, 1200, 350), $article->getTitle());
                /** @var ArticleImageInterface $image */
                $image = $this->articleImageFactory->createNew();
                $image->setFile($srcImage);
                $article->addImage($image);

                $this->imageUploader->uploadImages(new ResourceControllerEvent($article));

                // Add comments
                if (rand(0, 100) > 50) {
                    $this->addArticleComments($article);
                }

                $this->objectManager->persist($article);
            }
        }

        $this->objectManager->flush();
    }

    private function createArticleCategories(): void
    {
        for ($i = 0; $i < 5; $i++) {
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

            $this->objectManager->persist($articleCategory);
            $this->categories[] = $articleCategory;
        }
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
     * @return \Generator
     */
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
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('articles_per_channel')->isRequired()->min(1)->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'blog';
    }
}
