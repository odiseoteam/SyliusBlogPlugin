<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Extension;

use Odiseo\BlogBundle\Form\Type\ArticleType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'odiseo_sylius_blog_plugin.form.article.channels',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [ArticleType::class];
    }
}
