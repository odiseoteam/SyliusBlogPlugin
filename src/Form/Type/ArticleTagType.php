<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleTagType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ArticleTagTranslationType::class,
                'label' => 'sylius.ui.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_article_tag';
    }
}
