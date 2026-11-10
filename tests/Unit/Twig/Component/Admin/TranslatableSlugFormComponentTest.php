<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Twig\Component\Admin;

use Odiseo\SyliusBlogPlugin\Entity\ArticleTag;
use Odiseo\SyliusBlogPlugin\Form\Type\ArticleTagType;
use Odiseo\SyliusBlogPlugin\Twig\Component\Admin\TranslatableSlugFormComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Generator\SlugGenerator;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;

#[CoversClass(TranslatableSlugFormComponent::class)]
final class TranslatableSlugFormComponentTest extends TestCase
{
    public function testItGeneratesTheSlugOfALocaleFromItsName(): void
    {
        $component = $this->component();
        $component->formValues = ['translations' => [
            'en_US' => ['name' => 'Café brewing tips', 'slug' => ''],
            'es_AR' => ['name' => 'Consejos', 'slug' => 'consejos'],
        ]];

        $component->generateSlug('en_US');

        self::assertSame('cafe-brewing-tips', $this->slug($component, 'en_US'));
        self::assertSame('consejos', $this->slug($component, 'es_AR'));
    }

    public function testWithoutANameTheSlugIsEmpty(): void
    {
        $component = $this->component();

        $component->generateSlug('en_US');

        self::assertSame('', $this->slug($component, 'en_US'));
    }

    public function testTheSlugCanComeFromAnotherField(): void
    {
        $component = $this->component('title');
        $component->formValues = ['translations' => ['en_US' => ['title' => 'Brewing guide', 'name' => 'Ignored']]];

        $component->generateSlug('en_US');

        self::assertSame('brewing-guide', $this->slug($component, 'en_US'));
    }

    private function slug(TranslatableSlugFormComponent $component, string $localeCode): mixed
    {
        /** @var array{translations: array<string, array{slug?: mixed}>} $values */
        $values = $component->formValues;

        return $values['translations'][$localeCode]['slug'] ?? null;
    }

    private function component(string $sourceField = 'name'): TranslatableSlugFormComponent
    {
        /** @var RepositoryInterface<ResourceInterface>&Stub $repository */
        $repository = $this->createStub(RepositoryInterface::class);

        return new TranslatableSlugFormComponent(
            $repository,
            $this->createStub(FormFactoryInterface::class),
            ArticleTag::class,
            ArticleTagType::class,
            new SlugGenerator(),
            $sourceField,
        );
    }
}
