<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig\Component\Admin;

use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

/** Admin form of a resource whose slug, per locale, is generated from a translated field (its name by default). */
#[AsLiveComponent]
final class TranslatableSlugFormComponent
{
    /** @use ResourceFormComponentTrait<ResourceInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    /**
     * @param RepositoryInterface<ResourceInterface> $repository
     * @param class-string $resourceClass
     * @param class-string $formClass
     */
    public function __construct(
        RepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly string $sourceField = 'name',
    ) {
        $this->initialize($repository, $formFactory, $resourceClass, $formClass);
    }

    #[LiveAction]
    public function generateSlug(#[LiveArg] string $localeCode): void
    {
        /** @var array{translations?: array<string, array<string, mixed>>} $values */
        $values = $this->formValues;
        $source = $values['translations'][$localeCode][$this->sourceField] ?? '';

        $values['translations'][$localeCode]['slug'] = $this->slugGenerator->generate(is_string($source) ? $source : '');
        $this->formValues = $values;
    }
}
