<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute currencyLabelValue
 */
class Currency extends Input
{
    use Shadow\Currency;

    public ?string $currencyLabelValue = null;
    public HtmlTag $currencyLabel;

    /** @var HtmlTag */
    public HtmlTag $currencyInput;

    /**
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->currencyLabel = Html::label(['class' => 'currency-symbol']);
        $this->currencyInput = Html::input([
            'type' => 'number',
            'name' => $this->getName(),
            'id' => $this->getId(),
            'step' => 'any',
            'min' => '0'
        ]);
        $this->addControl($this->currencyInput, [
            $this->currencyLabel,
            ' ',
            $this->currencyInput
        ]);
    }

    /**
     * @templateMethod
     * @param string $currency
     * @return static
     * @throws \Exception
     */
    public function currency(string $currency): static
    {
        $this->currencyLabelValue = $currency;
        $this->currencyLabel->add($currency);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(): void
    {
        if ($this->currencyLabelValue != null) {
            $this->currency($this->currencyLabelValue);
        }
    }
}
