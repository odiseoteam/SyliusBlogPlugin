<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig\Component\Admin;

use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;

/** Author form: its slug is generated from its name, never from the username of the linked administrator. */
#[AsLiveComponent]
final class AuthorFormComponent
{
    use LiveCollectionTrait;

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
    ) {
        $this->initialize($repository, $formFactory, $resourceClass, $formClass);
    }

    #[LiveAction]
    public function generateSlug(): void
    {
        $name = $this->formValues['name'] ?? '';

        $this->formValues['slug'] = $this->slugGenerator->generate(is_string($name) ? $name : '');
    }
}
