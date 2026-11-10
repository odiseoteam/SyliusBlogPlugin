<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

#[AsEntityAutocompleteField(
    alias: 'odiseo_blog_admin_article_category',
    route: 'sylius_admin_entity_autocomplete',
)]
final class ArticleCategoryAutocompleteType extends AbstractType
{
    public function __construct(private readonly string $categoryClass)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->categoryClass,
            'choice_label' => 'name',
        ]);

        $resolver->setAllowedTypes('extra_options', 'array');

        $resolver->addNormalizer('filter_query', function (Options $options, ?callable $filterQuery): ?callable {
            /** @var array{excluded_category_codes?: list<string>} $extraOptions */
            $extraOptions = $options['extra_options'];
            $excludedCodes = $extraOptions['excluded_category_codes'] ?? [];
            if ([] === $excludedCodes) {
                return $filterQuery;
            }

            return static function (QueryBuilder $queryBuilder, string $query, EntityRepository $repository) use ($filterQuery, $excludedCodes): void {
                if (null !== $filterQuery) {
                    $filterQuery($queryBuilder, $query, $repository);
                }

                $queryBuilder
                    ->andWhere('entity.code NOT IN (:excluded_codes)')
                    ->setParameter('excluded_codes', $excludedCodes)
                ;
            };
        });
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_admin_article_category_autocomplete';
    }

    public function getParent(): string
    {
        return TranslatableAutocompleteType::class;
    }
}
