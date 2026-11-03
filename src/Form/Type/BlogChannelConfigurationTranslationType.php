<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class BlogChannelConfigurationTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'odiseo_blog.form.channel_configuration.title',
                'help' => 'odiseo_blog.form.channel_configuration.title_help',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'odiseo_blog.form.channel_configuration.description',
                'help' => 'odiseo_blog.form.channel_configuration.description_help',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_channel_configuration_translation';
    }
}
