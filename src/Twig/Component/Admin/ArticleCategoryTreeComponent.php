<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig\Component\Admin;

use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleCategoryRepositoryInterface;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/** Data and reordering of the category tree, drawn by Sylius' taxon tree controller. */
#[AsLiveComponent]
final class ArticleCategoryTreeComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    /** @param ArticleCategoryRepositoryInterface<ArticleCategoryInterface> $categoryRepository */
    public function __construct(
        private readonly ArticleCategoryRepositoryInterface $categoryRepository,
        private readonly ObjectManager $categoryManager,
        private readonly LocaleContextInterface $localeContext,
        private readonly TranslationLocaleProviderInterface $translationLocaleProvider,
    ) {
    }

    /** @return list<array{id: int, name: string, enabled: true, children: list<mixed>}> */
    public function getTree(): array
    {
        $fallbackLocaleCode = $this->translationLocaleProvider->getDefaultLocaleCode();

        try {
            $localeCode = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            $localeCode = $fallbackLocaleCode;
        }

        // Deepest first, so each category's children are built before it.
        $tree = [];
        $children = [];
        foreach ($this->categoryRepository->findAllForTree($localeCode, $fallbackLocaleCode) as $category) {
            $node = [
                'id' => $category['id'],
                'name' => $category['name'],
                // Categories can't be disabled; the controller greys out disabled nodes.
                'enabled' => true,
                'children' => $children[$category['id']] ?? [],
            ];

            if (null === $category['parentId']) {
                $tree[] = $node;
            } else {
                $children[$category['parentId']][] = $node;
            }
        }

        return $tree;
    }

    #[LiveAction]
    public function moveUp(#[LiveArg] int $categoryId): void
    {
        $category = $this->categoryRepository->find($categoryId);
        if (null === $category || 0 >= (int) $category->getPosition()) {
            return;
        }

        $category->setPosition((int) $category->getPosition() - 1);
        $this->categoryManager->flush();
    }

    #[LiveAction]
    public function moveDown(#[LiveArg] int $categoryId): void
    {
        $category = $this->categoryRepository->find($categoryId);
        if (null === $category) {
            return;
        }

        $category->setPosition((int) $category->getPosition() + 1);
        $this->categoryManager->flush();
    }
}
