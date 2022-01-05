<?php
namespace Deform\Component;

use Deform\Html\Html as Html;

/**
 */
class Currency extends BaseComponent
{
    public $currencyLabel;

    /**
     * @throws \Exception
     */
    public function setup()
    {
        $this->currencyLabel = Html::label(['class'=>'currency-symbol']);

        $currencyInput = Html::input([
            'type'=>'text',
            'name' => $this->getName(),
            'id' => $this->getId()
        ]);

        $this->addControl($this->currencyLabel);
        $this->addControl(' ');
        $this->addControl($currencyInput);
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function currency(string $currency): Currency
    {
        $this->currencyLabel->add($currency);
        return $this;
    }

    public function beforeRender()
    {
        if ($this->currencyLabel->isEmpty()) {
            $this->currencyLabel->add('&pound;');
        }
    }
}
