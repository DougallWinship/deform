<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;

/**
 * @method $this min(int $min)
 * @method $this max(int $max)
 * @method $this step(mixed $step) usually an int a float or 'any'
 */
class Slider extends Input
{
    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->input->type('range');
    }

    /**
     * whether to add an output tag showing the current value
     * @param bool $showOutput
     * @return void|static
     */
    public function showOutput(bool $showOutput = true): ?static
    {
        if ($showOutput) {
            $this->componentContainer->control->addHtmlTag(Html::output(['class' => 'slider-output']));
            $input = $this->componentContainer->control->getControls()[0];
            $input->oninput("this.nextElementSibling.value=this.value");
            return $this;
        }
    }
}
