<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'odiseo_blog.form.article.title',
            ])
            ->add('slug', TextType::class, [
                'label' => 'sylius.ui.slug',
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'odiseo_blog.form.article.excerpt',
                'help' => 'odiseo_blog.form.article.excerpt_help',
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'odiseo_blog.form.article.content',
                'help' => 'odiseo_blog.form.article.content_help',
                'required' => false,
                'attr' => ['rows' => 12, 'class' => 'font-monospace'],
            ])
            ->add('imageAlt', TextType::class, [
                'label' => 'odiseo_blog.form.article.image_alt',
                'help' => 'odiseo_blog.form.article.image_alt_help',
                'required' => false,
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
        return 'odiseo_blog_article_translation';
    }
}
