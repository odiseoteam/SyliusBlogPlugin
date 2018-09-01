<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Behaviour\ContainsErrorTrait;

class CreatePage extends BaseCreatePage implements CreatePageInterface
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
        var_dump($this->getDocument()->find('#odiseo_blog_article_translations_en_US_slug'));
        $this->getDocument()->fillField('odiseo_blog_article_translations_en_US_slug', $slug);
    }

    /**
     * @inheritdoc
     */
    public function fillTitle($title)
    {
        $this->getDocument()->fillField('Title', $title);
    }

    /**
     * @inheritdoc
     */
    public function fillContent($content)
    {
        $this->getDocument()->fillField('Content', $content);
    }
}
