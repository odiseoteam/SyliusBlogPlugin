<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Twig\Component\Admin;

use Odiseo\SyliusBlogPlugin\Entity\Author;
use Odiseo\SyliusBlogPlugin\Form\Type\AuthorType;
use Odiseo\SyliusBlogPlugin\Twig\Component\Admin\AuthorFormComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Generator\SlugGenerator;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;

#[CoversClass(AuthorFormComponent::class)]
final class AuthorFormComponentTest extends TestCase
{
    public function testTheSlugComesFromTheName(): void
    {
        $component = $this->component();
        $component->formValues = ['name' => 'Jane Döe', 'slug' => '', 'adminUser' => '3'];

        $component->generateSlug();

        self::assertSame('jane-doe', $component->formValues['slug']);
        self::assertSame('3', $component->formValues['adminUser']);
    }

    public function testWithoutANameTheSlugIsEmpty(): void
    {
        $component = $this->component();

        $component->generateSlug();

        self::assertSame('', $component->formValues['slug']);
    }

    private function component(): AuthorFormComponent
    {
        /** @var RepositoryInterface<ResourceInterface>&Stub $repository */
        $repository = $this->createStub(RepositoryInterface::class);

        return new AuthorFormComponent(
            $repository,
            $this->createStub(FormFactoryInterface::class),
            Author::class,
            AuthorType::class,
            new SlugGenerator(),
        );
    }
}
