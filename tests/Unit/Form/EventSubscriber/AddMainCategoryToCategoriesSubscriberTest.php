<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Form\EventSubscriber;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Form\EventSubscriber\AddMainCategoryToCategoriesSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

#[CoversClass(AddMainCategoryToCategoriesSubscriber::class)]
final class AddMainCategoryToCategoriesSubscriberTest extends TestCase
{
    public function testItRunsBeforeTheValidation(): void
    {
        self::assertSame(
            [FormEvents::POST_SUBMIT => ['addMainCategory', 10]],
            AddMainCategoryToCategoriesSubscriber::getSubscribedEvents(),
        );
    }

    public function testTheMainCategoryIsAddedToTheCategories(): void
    {
        $tea = new ArticleCategory();
        $coffee = new ArticleCategory();
        $article = new Article();
        $article->addCategory($tea);
        $article->setMainCategory($coffee);

        $this->submit($article);
        $this->submit($article);

        self::assertSame([$tea, $coffee], $article->getCategories()->toArray());
    }

    public function testWithoutAMainCategoryNothingChanges(): void
    {
        $article = new Article();

        $this->submit($article);

        self::assertTrue($article->getCategories()->isEmpty());
    }

    private function submit(mixed $data): void
    {
        (new AddMainCategoryToCategoriesSubscriber())->addMainCategory(
            new FormEvent($this->createStub(FormInterface::class), $data),
        );
    }
}
