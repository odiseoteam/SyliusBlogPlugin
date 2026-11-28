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

    #[Then('I should see the image :alt in it')]
    public function iShouldSeeTheImageInIt(string $alt): void
    {
        Assert::true($this->showPage->hasImage($alt));
    }

    #[Then('I should see a gallery of :count images in it')]
    public function iShouldSeeAGalleryOfImagesInIt(int $count): void
    {
        Assert::same($this->showPage->countGalleryImages(), $count);
    }
}
