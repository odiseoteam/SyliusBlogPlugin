<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Shop\Article;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

/** The minimal article page of the test application. */
final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'odiseo_blog_test_article_show';
    }

    public function hasProduct(string $name): bool
    {
        foreach ($this->getElement('content')->findAll('css', '.odiseo-blog-product') as $product) {
            if (str_contains($product->getText(), $name)) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = array_merge(parent::getDefinedElements(), [
            'content' => '[data-test-article-content]',
        ]);

        return $elements;
    }
}
