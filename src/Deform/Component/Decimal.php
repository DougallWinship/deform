<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;

class Decimal extends Input
{
    use Shadow\Decimal;
    public const string ROUND_STANDARD = "standard";
    public const string ROUND_CEIL = "ceil";
    public const string ROUND_FLOOR = "floor";
    public const string ROUND_BANKERS = "bankers";

    public function setup(): void
    {
        $js = <<<JS
if (!isNaN(this.value)) {
    let val = parseFloat(this.value);
    const dp = parseInt(this.dataset.dp);
    const min = this.dataset.min !== undefined ? parseFloat(this.dataset.min) : null;
    const max = this.dataset.max !== undefined ? parseFloat(this.dataset.max) : null;
    if (min !== null && val < min) val = min;
    if (max !== null && val > max) val = max;
    this.value = Deform.round(val, this.dataset.round, dp);
}
JS;
        $this->input = Html::input([
            'type' => 'text',
            'name' => $this->getName(),
            'id' => $this->getId(),
            'inputmode' => 'decimal',
            'pattern' => "^\-?d+(\.\d{1,2})?$",
            'placeholder' => "0.00",
            'data-round' => self::ROUND_STANDARD,
            'data-dp' => 2,
            'onchange' => \Deform\Util\Strings::trimInternal($js),
        ]);
        $this->addControl($this->input);
    }

    public function min($minValue = 0): self
    {
        if (!is_numeric($minValue)) {
            throw new \Exception("'min' must be a numeric value");
        }
        $maxValue = $this->input->get('data-max');
        if ($maxValue!==null && floatVal($minValue) >= floatval($maxValue)) {
            throw new \Exception("'min' must be less than 'max'");
        }
        $this->input->set('data-min', (string)$minValue);
        return $this;
    }

    public function max($maxValue = 0): self
    {
        if (!is_numeric($maxValue)) {
            throw new \Exception("'max' must be a numeric value");
        }
        $minValue = $this->input->get('data-min');
        if ($minValue!==null && floatval($maxValue) <= floatval($minValue)) {
            throw new \Exception("'max' must be greater than 'min'");
        }
        $this->input->set('data-max', (string)$maxValue);
        return $this;
    }

    public function roundStrategy(string $roundingStrategy): self
    {
        $this->input->set('data-round', $roundingStrategy);
        return $this;
    }

    public function dp(int $decimalPlaces=2): self
    {
        $this->input->set('data-dp', $decimalPlaces);

        $min = $this->input->get('data-min');
        $max = $this->input->get('data-max');
        if ($min == 0) {
            $negativePattern = "";
        }
        elseif ($max == 0) {
            $negativePattern = "-";
        }
        else {
            $negativePattern = "-?";
        }
        $escapedDecimal = preg_quote('.', '/'); // safer if you later allow comma/locale rules
        $pattern = '^'.$negativePattern.'\\d+(' . $escapedDecimal . '\\d{1,' . $decimalPlaces . '})?$';
        $this->input->set('pattern', $pattern);
        return $this;
    }
}