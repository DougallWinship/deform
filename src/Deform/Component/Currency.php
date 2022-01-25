<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute currencyLabelValue
 */
class Currency extends BaseComponent
{
    public string $currencyLabelValue;
    public HtmlTag $currencyLabel;

    /**
     * @throws \Exception
     */
    public function setup()
    {
        $this->currencyLabel = Html::label(['class' => 'currency-symbol']);
        $currencyInput = Html::input([
            'type' => 'text',
            'name' => $this->getName(),
            'id' => $this->getId()
        ]);
        $this->control([
            $this->currencyLabel,
            ' ',
            $currencyInput
        ]);
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function currency(string $currency): Currency
    {
        $this->currencyLabelValue = $currency;
        $this->currencyLabel->add($currency);
        return $this;
    }
}
