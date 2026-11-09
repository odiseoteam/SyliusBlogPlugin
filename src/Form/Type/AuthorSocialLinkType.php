<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** A link of an author, as an array: the network and the URL. */
final class AuthorSocialLinkType extends AbstractType
{
    /** @param list<string> $networks */
    public function __construct(private readonly array $networks)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        foreach ($this->networks as $network) {
            $choices['odiseo_blog.social_network.' . $network] = $network;
        }

        $builder
            ->add('network', ChoiceType::class, [
                'label' => 'odiseo_blog.form.author.network',
                'choices' => $choices,
                'placeholder' => 'odiseo_blog.form.author.choose_network',
            ])
            ->add('url', UrlType::class, [
                'label' => 'odiseo_blog.form.author.url',
                'default_protocol' => 'https',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_author_social_link';
    }
}
