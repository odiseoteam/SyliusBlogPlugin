<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleTag;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\TranslatableSlugFormTrait;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use TranslatableSlugFormTrait;
}
