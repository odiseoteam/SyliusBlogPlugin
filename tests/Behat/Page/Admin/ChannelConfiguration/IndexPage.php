<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ChannelConfiguration;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function isBlogShownIn(string $channelName): bool
    {
        return $this->getCellForResource('enabled', ['channel' => $channelName])->has('css', '[data-test-status-enabled]');
    }

    public function getPostsPerPageOf(string $channelName): int
    {
        return (int) trim($this->getCellForResource('postsPerPage', ['channel' => $channelName])->getText());
    }
}
