<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Service\DriverHelper;
use Webmozart\Assert\Assert;

/** Picks or removes options of a Symfony UX autocomplete (Tom Select) by their label. */
trait AutocompleteTrait
{
    /**
     * The search is typed in the input, as a user does: Tom Select's virtual scroll drops the answer to a query
     * that isn't the one in the input, so calling load() directly finds nothing.
     */
    private function selectInAutocomplete(NodeElement $select, string $label): void
    {
        $tomSelect = $this->tomSelectOf($select);
        $driver = $this->getDriver();
        $text = json_encode($label);

        $driver->executeScript(sprintf(
            '(function (ts) { ts.focus(); ts.control_input.value = %s; ts.control_input.dispatchEvent(new Event("input", {bubbles: true})); })(%s);',
            $text,
            $tomSelect,
        ));

        $found = $driver->wait(10000, sprintf(
            '(function (ts) { return Object.values(ts.options).some((o) => o.text === %s); })(%s)',
            $text,
            $tomSelect,
        ));
        Assert::true($found, sprintf(
            'The autocomplete has no option "%s". Options: %s.',
            $label,
            json_encode($driver->evaluateScript(sprintf('Object.values(%s.options).map((o) => o.text)', $tomSelect))),
        ));

        $driver->executeScript(sprintf(
            '(function (ts) { ts.addItem(Object.values(ts.options).find((o) => o.text === %s).value); ts.setTextboxValue(""); ts.close(); })(%s);',
            $text,
            $tomSelect,
        ));

        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    private function removeFromAutocomplete(NodeElement $select, string $label): void
    {
        $this->getDriver()->executeScript(sprintf(
            '(function (ts) { const value = ts.items.find((v) => ts.options[v].text === %s); ts.removeItem(value); })(%s);',
            json_encode($label),
            $this->tomSelectOf($select),
        ));

        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    /** The JavaScript expression of the Tom Select instance of the field. */
    private function tomSelectOf(NodeElement $select): string
    {
        return sprintf(
            'document.evaluate(%s, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.tomselect',
            json_encode($select->getXpath()),
        );
    }
}
