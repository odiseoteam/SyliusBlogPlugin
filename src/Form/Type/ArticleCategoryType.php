<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleCategoryType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $builder->getData();

        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('parent', ArticleCategoryAutocompleteType::class, [
                'label' => 'sylius.ui.parent',
                'required' => false,
                // The category and its subcategories can't be its parent.
                'extra_options' => [
                    'excluded_category_codes' => $category instanceof ArticleCategoryInterface ? $this->codesOf($category) : [],
                ],
            ])
            ->add('image', ArticleCategoryImageType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ArticleCategoryTranslationType::class,
                'label' => 'sylius.ui.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_article_category';
    }

    /** @return list<string> */
    private function codesOf(ArticleCategoryInterface $category): array
    {
        $codes = null === $category->getCode() ? [] : [$category->getCode()];
        foreach ($category->getChildren() as $child) {
            $codes = [...$codes, ...$this->codesOf($child)];
        }

        return $codes;
    }
}
