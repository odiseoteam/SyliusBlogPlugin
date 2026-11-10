<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Odiseo\SyliusBlogPlugin\Form\EventSubscriber\AddMainCategoryToCategoriesSubscriber;
use Odiseo\SyliusBlogPlugin\Form\EventSubscriber\PreselectChannelsSubscriber;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleType extends AbstractResourceType
{
    /**
     * @param class-string $dataClass
     * @param string[] $validationGroups
     * @param RepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private readonly RepositoryInterface $channelRepository,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->addEventSubscriber(new PreselectChannelsSubscriber($this->channelRepository))
            ->addEventSubscriber(new AddMainCategoryToCategoriesSubscriber())
            ->add('channels', ChannelChoiceType::class, [
                'label' => 'sylius.ui.channels',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('featured', CheckboxType::class, [
                'label' => 'odiseo_blog.form.article.featured',
                'required' => false,
            ])
            ->add('commentsEnabled', CheckboxType::class, [
                'label' => 'odiseo_blog.form.article.comments_enabled',
                'required' => false,
            ])
            ->add('mainCategory', ArticleCategoryAutocompleteType::class, [
                'label' => 'odiseo_blog.form.article.main_category',
                'help' => 'odiseo_blog.form.article.main_category_help',
                'required' => false,
            ])
            ->add('categories', ArticleCategoryAutocompleteType::class, [
                'label' => 'odiseo_blog.ui.article_categories',
                'multiple' => true,
                'required' => false,
            ])
            ->add('tags', ArticleTagAutocompleteType::class, [
                'label' => 'odiseo_blog.ui.article_tags',
                'multiple' => true,
                'required' => false,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ArticleTranslationType::class,
                'label' => 'sylius.ui.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_article';
    }
}
