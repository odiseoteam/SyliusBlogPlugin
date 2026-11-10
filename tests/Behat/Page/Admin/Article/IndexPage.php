<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\AutocompleteTrait;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    use AutocompleteTrait;

    public function search(string $phrase): void
    {
        $this->getElement('search_filter')->setValue($phrase);
        $this->filter();
    }

    public function chooseStateFilter(string $state): void
    {
        $this->getElement('state_filter')->selectOption($state);
    }

    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('channel_filter')->selectOption($channelName);
    }

    public function choosePublishedBetween(string $from, string $to): void
    {
        $this->getDocument()->fillField('criteria_published_at_from_date', $from);
        $this->getDocument()->fillField('criteria_published_at_to_date', $to);
    }

    public function chooseCategoryFilter(string $categoryName): void
    {
        if (!$this->areFiltersVisible()) {
            $this->toggleFilters();
        }

        $this->selectInAutocomplete($this->getElement('category_filter'), $categoryName);
    }

    public function chooseAuthorFilter(string $authorName): void
    {
        if (!$this->areFiltersVisible()) {
            $this->toggleFilters();
        }

        $this->selectInAutocomplete($this->getElement('author_filter'), $authorName);
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = array_merge(parent::getDefinedElements(), [
            'author_filter' => '#criteria_author',
            'category_filter' => '#criteria_category',
            'channel_filter' => '#criteria_channel',
            'search_filter' => '#criteria_search_value',
            'state_filter' => '#criteria_state',
        ]);

        return $elements;
    }
}
