<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Behaviour\ContainsErrorTrait;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ContainsErrorTrait;

    /**
     * @inheritdoc
     */
    public function fillCode($code)
    {
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @inheritdoc
     */
    public function fillSlug($slug)
    {
        $this->getDocument()->fillField('Slug', $slug);
    }

    /**
     * @inheritdoc
     */
    public function fillTitle($title)
    {
        $this->getDocument()->fillField('Title', $title);
    }
}
