<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

/** Administrators that aren't the author of someone else yet; the "author_id" extra option is the current one. */
#[AsEntityAutocompleteField(
    alias: 'odiseo_blog_admin_admin_user',
    route: 'sylius_admin_entity_autocomplete',
)]
final class AdminUserAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $adminUserClass,
        private readonly string $authorClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->adminUserClass,
            'choice_label' => 'username',
        ]);

        $resolver->setAllowedTypes('extra_options', 'array');

        // The search is part of the filter, as the autocomplete doesn't take both a filter and the searchable fields.
        $resolver->setNormalizer('filter_query', function (Options $options): callable {
            /** @var array{author_id?: int|null} $extraOptions */
            $extraOptions = $options['extra_options'];
            $authorId = $extraOptions['author_id'] ?? null;

            return function (QueryBuilder $queryBuilder, string $query, EntityRepository $repository) use ($authorId): void {
                if ('' !== $query) {
                    $queryBuilder
                        ->andWhere('LOWER(entity.username) LIKE :search OR LOWER(entity.email) LIKE :search')
                        ->setParameter('search', '%' . addcslashes(mb_strtolower($query), '%_\\') . '%')
                    ;
                }

                $linked = $queryBuilder->getEntityManager()->createQueryBuilder()
                    ->select('IDENTITY(linked.adminUser)')
                    ->from($this->authorClass, 'linked')
                    ->where('linked.adminUser IS NOT NULL')
                ;
                if (null !== $authorId) {
                    $linked->andWhere('linked.id != :author_id');
                    $queryBuilder->setParameter('author_id', $authorId);
                }

                $queryBuilder->andWhere($queryBuilder->expr()->notIn('entity.id', $linked->getDQL()));
            };
        });
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_admin_user_autocomplete';
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
