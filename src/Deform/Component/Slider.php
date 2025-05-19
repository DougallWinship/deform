<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformException;
use Deform\Html\Html;

/**
 * @method $this min(int $min)
 * @method $this max(int $max)
 * @method $this step(mixed $step) int or 'any'
 */
class Slider extends Input
{
    use Shadow\Slider;

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
     * @throws DeformException
     */
    public function showOutput(bool $showOutput = true): ?static
    {
        if ($showOutput) {
            $value = $this->attributes['value'] ?? "";
            if (!$value) {
                $min = $this->attributes['min'] ?? 0;
                $max = $this->attributes['max'] ?? 100;
                $step = $this->attributes['step'] ?? 1;
                // calculate default
                $mid = ($min + $max) / 2;
                $steps = round(($mid - $min) / $step);
                $default = $min + $steps * $step;
                $value = $default;
            }
            $this->componentContainer->control->addHtmlTag(
                Html::output(['class' => 'slider-output', 'style' => 'width:100%;text-align:center;display:block'])
                    ->add($value)
            );
            $input = $this->componentContainer->control->getControls()[0];
            $input->oninput("this.nextElementSibling.value=this.value");
            return $this;
        }
    }
}
