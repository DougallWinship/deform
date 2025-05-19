<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute currencyLabelValue
 * @persistAttribute min
 * @persistAttribute max
 * @persistAttribute dp
 * @persistAttribute strategy
 */
class Currency extends Decimal
{
    use Shadow\Currency;

    public ?string $currencyLabelValue = null;
    public HtmlTag $currencyLabel;

    /** @var HtmlTag */
    public HtmlTag $currencyInput;

    /**
     * @inheritdoc
     */
    public function setup(): void
    {
        $this->dp = 2; // default
        parent::setup();
        $this->currencyLabel = Html::label(['class' => 'currency-symbol']);
        $this->replaceControl($this->input, [
            $this->currencyLabel,
            ' ',
            $this->input
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
        parent::hydrate();
        if ($this->currencyLabelValue != null) {
            $this->currency($this->currencyLabelValue);
        }
    }
}
