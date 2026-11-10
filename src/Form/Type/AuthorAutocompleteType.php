<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

/** Enabled authors, searched by name and code. Several of them are a list, in the order they were submitted. */
#[AsEntityAutocompleteField(
    alias: 'odiseo_blog_admin_author',
    route: 'sylius_admin_entity_autocomplete',
)]
final class AuthorAutocompleteType extends AbstractType
{
    public function __construct(private readonly string $authorClass)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (true !== $options['multiple']) {
            return;
        }

        $builder->addModelTransformer(new CallbackTransformer(
            static fn (mixed $authors): mixed => $authors,
            static fn (mixed $authors): mixed => $authors instanceof Collection ? array_values($authors->toArray()) : $authors,
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->authorClass,
            'choice_label' => 'name',
            // Otherwise a multiple choice merges the submitted authors into the previous ones and keeps the previous order.
            'by_reference' => false,
            // The search is part of the filter, as the autocomplete doesn't take both a filter and the searchable fields.
            'filter_query' => static function (QueryBuilder $queryBuilder, string $query, EntityRepository $repository): void {
                $queryBuilder->andWhere('entity.enabled = true');

                if ('' !== $query) {
                    $queryBuilder
                        ->andWhere('LOWER(entity.name) LIKE :search OR LOWER(entity.code) LIKE :search')
                        ->setParameter('search', '%' . addcslashes(mb_strtolower($query), '%_\\') . '%')
                    ;
                }
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_admin_author_autocomplete';
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
