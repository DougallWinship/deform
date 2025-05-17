<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;

class Integer extends Input
{
    use Shadow\Integer;

    /**
     * @return void
     * @throws \Exception
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
            'pattern' => "^-?\\d+$",
            'step' => '1',
            'placeholder' => "0",
            'oninput' => \Deform\Util\Strings::trimInternal($js),
        ]);
        $this->addControl($this->input);
    }

    /**
     * @param $minValue
     * @return $this
     * @throws \Exception
     */
    public function min($minValue): self
    {
        if ($minValue === null) {
            $this->input->unset('data-min');
        }
        if (!is_numeric($minValue)) {
            throw new \Exception("'min' must be a numeric value");
        }
        $minValue = intval($minValue);
        $maxValue = $this->input->get('data-max');
        if ($maxValue !== null) {
            $maxValue = intval($maxValue);
            if ($minValue >= $maxValue) {
                throw new \Exception("'min' must be less than 'max'");
            }
        }
        $this->updatePattern($minValue, $maxValue);
        $this->input->set('data-min', (string)$minValue);
        return $this;
    }

    /**
     * @param $maxValue
     * @return $this
     * @throws \Exception
     */
    public function max($maxValue): self
    {
        if ($maxValue === null) {
            $this->input->unset('data-max');
            return $this;
        }
        if (!is_numeric($maxValue)) {
            throw new \Exception("'max' must be a numeric value");
        }
        $maxValue = intval($maxValue);

        $minValue = $this->input->get('data-min');
        if ($minValue !== null) {
            $minValue = intval($minValue);
            if ($maxValue <= $minValue) {
                throw new \Exception("'max' must be greater than 'min'");
            }
        }
        $this->updatePattern($minValue, $maxValue);
        $this->input->set('data-max', (string)$maxValue);
        return $this;
    }

    /**
     * @param $stepValue
     * @return $this
     * @throws \Exception
     */
    public function step($stepValue = null): self
    {
        if ($stepValue === null) {
            $this->input->unset('data-step');
            return $this;
        }
        if (!is_numeric($stepValue)) {
            throw new \Exception("'step' must be a +ve numeric value");
        }
        $stepValue = intval($stepValue);
        $this->input->set('step', (string)$stepValue);
        return $this;
    }

    /**
     * @param int|null $min
     * @param int|null $max
     * @return void
     * @throws \Exception
     */
    private function updatePattern(?int $min, ?int $max): void
    {
        // Determine sign constraints
        if ($max === 0) {
            $signPattern = '-';   // Only negative
        } elseif ($min === 0) {
            $signPattern = '';    // Only positive
        } else {
            $signPattern = '-?';  // Allow both
        }

        $pattern = '^' . $signPattern . '\\d+$';
        $this->input->set('pattern', $pattern);
    }
}
