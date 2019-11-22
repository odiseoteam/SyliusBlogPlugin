<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Behaviour\ContainsErrorInterface;

interface CreatePageInterface extends BaseCreatePageInterface, ContainsErrorInterface
{
    /**
     * @param string $code
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillCode(string $code): void;

    /**
     * @param string $slug
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillSlug(string $slug): void;

    /**
     * @param string $title
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillTitle(string $title): void;

    /**
     * @param string $content
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillContent(string $content): void;
}
