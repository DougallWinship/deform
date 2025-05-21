<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformComponentException;
use Deform\Exception\DeformException;
use Deform\Html\Html as Html;

/**
 * @persistAttribute min
 * @persistAttribute max
 * @persistAttribute step
 */
class Integer extends Input
{
    use Shadow\Integer;

    public ?int $min = null;

    public ?int $max = null;

    public ?int $step = null;

    /**
     * @return void
     * @throws DeformException
     */
    public function setup(): void
    {
        $js = <<<JS
if (!isNaN(this.value)) {
    let val = parseInt(this.value);
    const step = parseInt(this.step);
    if (step && step > 1) {
        val = Math.round(val / step) * step;
    }
    const min = this.dataset.min !== undefined ? parseInt(this.dataset.min) : null;
    const max = this.dataset.max !== undefined ? parseInt(this.dataset.max) : null;
    if (min !== null && val < min) val = min;
    if (max !== null && val > max) val = max;
    this.value = val;
}
JS;
        $this->input = Html::input([
            'type' => 'number',
            'name' => $this->getName(),
            'id' => $this->getId(),
            'inputmode' => 'decimal',
            'step' => '1',
            'placeholder' => "0",
            'oninput' => \Deform\Util\Strings::trimInternal($js),
        ]);
        $this->updatePattern();
        $this->addControl($this->input);
    }

    /**
     * @param $minValue
     * @return $this
     * @throws DeformException
     */
    public function min($minValue): self
    {
        if (!is_numeric($minValue)) {
            throw new DeformComponentException("'min' must be a numeric value");
        }
        $minValue = (int)$minValue;
        $maxValue = $this->input->get('max');
        if ($maxValue !== null) {
            $maxValue = intval($maxValue);
            if ($minValue >= $maxValue) {
                throw new DeformComponentException("'min' must be less than 'max'");
            }
        }
        $this->min = $minValue;
        $this->input->set('min', (string)$this->min);
        $this->updatePattern();
        return $this;
    }

    /**
     * @param mixed $maxValue
     * @return $this
     * @throws DeformException
     */
    public function max(mixed $maxValue): self
    {
        if (!is_numeric($maxValue)) {
            throw new DeformComponentException("'max' must be a numeric value");
        }
        $maxValue = (int)$maxValue;
        $minValue = $this->input->get('min');
        if ($minValue !== null) {
            $minValue = intval($minValue);
            if ($maxValue <= $minValue) {
                throw new DeformComponentException("'max' must be greater than 'min'");
            }
        }
        $this->max = $maxValue;
        $this->input->set('max', (string)$this->max);
        $this->updatePattern();
        return $this;
    }

    /**
     * @param $stepValue
     * @return $this
     * @throws DeformException
     */
    public function step($stepValue = null): self
    {
        if (!is_numeric($stepValue)) {
            throw new DeformComponentException("'step' must be a +ve numeric value");
        }
        $stepValue = (int)$stepValue;
        if ($stepValue < 1) {
            throw new DeformComponentException("'step' must be greater than 0");
        }
        $this->step = $stepValue;
        $this->input->set('step', (string)$stepValue);
        return $this;
    }

    /**
     * @return void
     * @throws DeformException
     */
    private function updatePattern(): void
    {
        if ($this->max === null && $this->min === null) {
            $negativePattern = '-?';
        } elseif ($this->min === null && $this->max <= 0) {
            $negativePattern = '-';
        } elseif ($this->max === null && $this->min >= 0) {
            $negativePattern = '';
        } else {
            $negativePattern = '-?';
        }
        $escapedMinus = preg_quote($negativePattern, '/');
        $pattern = '^' . $escapedMinus . '\\d+$';
        $this->input->set('pattern', $pattern);
    }

    /**
     * @return void
     * @throws DeformException
     */
    public function hydrate(): void
    {
        if ($this->min !== null) {
            $this->min($this->min);
        }
        if ($this->max !== null) {
            $this->max($this->max);
        }
        if ($this->step !== null) {
            $this->step($this->step);
        }
    }
}
