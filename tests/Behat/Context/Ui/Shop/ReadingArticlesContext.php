<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Shop\Article\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ReadingArticlesContext implements Context
{
    public function __construct(private readonly ShowPageInterface $showPage)
    {
    }

    #[When('I read the :article article')]
    public function iReadTheArticle(ArticleInterface $article): void
    {
        $this->showPage->open(['_locale' => 'en_US', 'slug' => $article->getTranslation('en_US')->getSlug()]);
    }

    #[Then('I should see the product :product in it')]
    public function iShouldSeeTheProductInIt(ProductInterface $product): void
    {
        Assert::true($this->showPage->hasProduct((string) $product->getName()));
    }

    #[Then('I should not see the product :product in it')]
    public function iShouldNotSeeTheProductInIt(ProductInterface $product): void
    {
        Assert::false($this->showPage->hasProduct((string) $product->getName()));
    }
}
