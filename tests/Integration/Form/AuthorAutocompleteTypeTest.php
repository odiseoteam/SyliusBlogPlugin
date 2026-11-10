<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Form;

use Odiseo\SyliusBlogPlugin\Entity\Author;
use Odiseo\SyliusBlogPlugin\Form\Type\AuthorAutocompleteType;
use Symfony\Component\Form\FormFactoryInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class AuthorAutocompleteTypeTest extends DatabaseTestCase
{
    public function testTheAuthorsKeepTheOrderTheyWereSubmittedIn(): void
    {
        $jane = $this->author('order_jane');
        $john = $this->author('order_john');
        $this->entityManager->persist($jane);
        $this->entityManager->persist($john);
        $this->entityManager->flush();

        $form = $this->formFactory()->create(AuthorAutocompleteType::class, [$jane, $john], ['multiple' => true]);
        $form->submit([(string) $john->getId(), (string) $jane->getId()]);

        self::assertTrue($form->isSynchronized(), (string) $form->getTransformationFailure()?->getMessage());
        /** @var list<Author> $authors */
        $authors = $form->getData();
        self::assertSame(['order_john', 'order_jane'], array_map(static fn (Author $author): ?string => $author->getCode(), $authors));
    }

    public function testItOffersEnabledAuthorsByNameOrCode(): void
    {
        $jane = $this->author('offer_jane');
        $jane->setName('Jane Doe');
        $john = $this->author('offer_john');
        $john->setName('John Smith');
        $john->disable();
        $this->entityManager->persist($jane);
        $this->entityManager->persist($john);
        $this->entityManager->flush();

        self::assertSame(['offer_jane'], $this->offered(''));
        self::assertSame(['offer_jane'], $this->offered('DOE'));
        self::assertSame(['offer_jane'], $this->offered('offer_j'));
        self::assertSame([], $this->offered('smith'));
    }

    /** @return list<string> */
    private function offered(string $query): array
    {
        $filter = $this->formFactory()->createBuilder(AuthorAutocompleteType::class)->getForm()->getConfig()->getOption('filter_query');
        self::assertIsCallable($filter);

        $repository = $this->entityManager->getRepository(Author::class);
        $queryBuilder = $repository->createQueryBuilder('entity')
            ->andWhere('entity.code LIKE :prefix')
            ->setParameter('prefix', 'offer_%')
            ->orderBy('entity.code')
        ;
        $filter($queryBuilder, $query, $repository);

        /** @var list<Author> $authors */
        $authors = $queryBuilder->getQuery()->getResult();

        return array_map(static fn (Author $author): string => (string) $author->getCode(), $authors);
    }

    private function formFactory(): FormFactoryInterface
    {
        $formFactory = self::getContainer()->get('form.factory');
        self::assertInstanceOf(FormFactoryInterface::class, $formFactory);

        return $formFactory;
    }
}
