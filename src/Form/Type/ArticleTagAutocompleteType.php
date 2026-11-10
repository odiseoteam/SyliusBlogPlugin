<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

#[AsEntityAutocompleteField(
    alias: 'odiseo_blog_admin_article_tag',
    route: 'sylius_admin_entity_autocomplete',
)]
final class ArticleTagAutocompleteType extends AbstractType
{
    public function __construct(private readonly string $tagClass)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->tagClass,
            'choice_label' => 'name',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_admin_article_tag_autocomplete';
    }

    public function getParent(): string
    {
        return TranslatableAutocompleteType::class;
    }
}
