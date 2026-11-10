<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleTagTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'sylius.ui.slug',
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'odiseo_blog.form.meta_title',
                'required' => false,
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'odiseo_blog.form.meta_description',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_article_tag_translation';
    }
}
