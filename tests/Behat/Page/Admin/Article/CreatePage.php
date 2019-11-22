<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Behaviour\ContainsErrorTrait;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ContainsErrorTrait;

    /**
     * @inheritdoc
     */
    public function fillCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @inheritdoc
     */
    public function fillSlug(string $slug): void
    {
        $this->getDocument()->fillField('Slug', $slug);
    }

    /**
     * @inheritdoc
     */
    public function fillTitle(string $title): void
    {
        $this->getDocument()->fillField('Title', $title);
    }

    /**
     * @inheritdoc
     */
    public function fillContent(string $content): void
    {
        $this->getDocument()->fillField('Content', $content);
    }
}
