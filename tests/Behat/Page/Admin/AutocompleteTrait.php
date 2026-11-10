<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Service\DriverHelper;

/** Picks or removes options of a Symfony UX autocomplete (Tom Select) by their label. */
trait AutocompleteTrait
{
    private function selectInAutocomplete(NodeElement $select, string $label): void
    {
        $element = sprintf('document.evaluate(%s, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue', json_encode($select->getXpath()));
        $driver = $this->getDriver();

        $driver->executeScript(sprintf('%s.tomselect.load(%s);', $element, json_encode($label)));
        $driver->wait(5000, sprintf('%s.tomselect.loading === 0', $element));
        $driver->executeScript(sprintf(
            '(function (ts) { const option = Object.values(ts.options).find((o) => o.text === %s); ts.addItem(option.value); })(%s.tomselect);',
            json_encode($label),
            $element,
        ));

        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    private function removeFromAutocomplete(NodeElement $select, string $label): void
    {
        $element = sprintf('document.evaluate(%s, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue', json_encode($select->getXpath()));

        $this->getDriver()->executeScript(sprintf(
            '(function (ts) { const value = ts.items.find((v) => ts.options[v].text === %s); ts.removeItem(value); })(%s.tomselect);',
            json_encode($label),
            $element,
        ));

        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }
}
