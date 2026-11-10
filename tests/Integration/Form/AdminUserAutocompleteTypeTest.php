<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Form;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBlogPlugin\Form\Type\AdminUserAutocompleteType;
use Sylius\Component\Core\Model\AdminUser;
use Symfony\Component\Form\FormFactoryInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class AdminUserAutocompleteTypeTest extends DatabaseTestCase
{
    public function testItOffersTheAdministratorsWithoutAnAuthor(): void
    {
        $free = $this->adminUser('free_admin');
        $taken = $this->adminUser('taken_admin');
        $author = $this->author('taken_author');
        $author->setAdminUser($taken);
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        self::assertSame(['free_admin'], $this->offered(null, ''));
        // The author that has the administrator keeps offering it.
        self::assertSame(['free_admin', 'taken_admin'], $this->offered($author->getId(), ''));
        self::assertSame(['free_admin'], $this->offered(null, 'FREE'));
        self::assertSame([], $this->offered(null, 'taken'));
        self::assertNotNull($free->getId());
    }

    public function testTheSearchTakesEmailsAndEscapesWildcards(): void
    {
        $this->adminUser('plain_admin');
        $this->entityManager->flush();

        self::assertSame(['plain_admin'], $this->offered(null, 'plain_admin@example'));
        self::assertSame([], $this->offered(null, '%'));
    }

    /** @return list<string> */
    private function offered(?int $authorId, string $query): array
    {
        $formFactory = self::getContainer()->get('form.factory');
        self::assertInstanceOf(FormFactoryInterface::class, $formFactory);

        $filter = $formFactory
            ->createBuilder(AdminUserAutocompleteType::class, null, ['extra_options' => ['author_id' => $authorId]])
            ->getForm()
            ->getConfig()
            ->getOption('filter_query')
        ;
        self::assertIsCallable($filter);

        $repository = $this->entityManager->getRepository(AdminUser::class);
        $queryBuilder = $repository->createQueryBuilder('entity')
            ->andWhere('entity.username IN (:usernames)')
            ->setParameter('usernames', ['free_admin', 'taken_admin', 'plain_admin'])
            ->orderBy('entity.username')
        ;
        $filter($queryBuilder, $query, $repository);

        return array_map(
            static fn (AdminUser $user): string => (string) $user->getUsername(),
            $this->users($queryBuilder),
        );
    }

    /** @return list<AdminUser> */
    private function users(QueryBuilder $queryBuilder): array
    {
        /** @var list<AdminUser> $users */
        $users = $queryBuilder->getQuery()->getResult();

        return $users;
    }
}
