<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;

/**
 * @persistAttribute min
 * @persistAttribute max
 * @persistAttribute dp
 * @persistAttribute strategy
 */
class Decimal extends Input
{
    use Shadow\Decimal;

    public const string ROUND_STANDARD = "standard";
    public const string ROUND_CEIL = "ceil";
    public const string ROUND_FLOOR = "floor";
    public const string ROUND_BANKERS = "bankers";

    public const array ALL_ROUND_STRATEGIES = [
        self::ROUND_STANDARD,
        self::ROUND_CEIL,
        self::ROUND_FLOOR,
        self::ROUND_BANKERS,
    ];

    public int $dp = 0;

    public ?float $min = null;

    public ?float $max = null;
    public ?string $strategy = self::ROUND_STANDARD;

    private ?string $pattern = null;

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
    const rounded = Deform.round(val, this.dataset.round, dp);
    this.value = rounded;
}
JS;
        $this->input = Html::input([
            'type' => 'text',
            'name' => $this->getName(),
            'id' => $this->getId(),
            'inputmode' => 'decimal',
            'placeholder' => "0",
            'data-round' => self::ROUND_STANDARD,
            'data-dp' => $this->dp,
            'onchange' => \Deform\Util\Strings::trimInternal($js),
        ]);
        $this->updatePattern();
        $this->addControl($this->input);
    }

    public function min(mixed $minValue): self
    {
        if (!is_numeric($minValue)) {
            throw new \Exception("'min' must be a numeric value");
        }
        if ($this->max !== null && $minValue >= $this->max) {
            throw new \Exception("'min' must be less than 'max'");
        }
        $this->min = (float)$minValue;
        $this->input->set('data-min', (string)$this->min);
        $this->updatePattern();
        return $this;
    }

    public function max(mixed $maxValue): self
    {
        if (!is_numeric($maxValue)) {
            throw new \Exception("'max' must be a numeric value");
        }
        if ($this->min !== null && $maxValue <= $this->min) {
            throw new \Exception("'max' must be greater than 'min'");
        }
        $this->max = (float)$maxValue;
        $this->input->set('data-max', (string)$this->max);
        $this->updatePattern();
        return $this;
    }

    public function roundStrategy(string $roundingStrategy): self
    {
        if (!in_array($roundingStrategy, self::ALL_ROUND_STRATEGIES)) {
            throw new \Exception("Unrecognised round strategy '$roundingStrategy'");
        }
        $this->strategy = $roundingStrategy;
        $this->input->set('data-round', $roundingStrategy);
        return $this;
    }

    public function dp(int $decimalPlaces): self
    {
        if ($decimalPlaces < 0) {
            throw new \Exception("'$decimalPlaces' must be greater than 0");
        }
        $this->dp = $decimalPlaces;
        $this->input->set('data-dp', $decimalPlaces);
        $this->updatePattern();
        return $this;
    }

    private function updatePattern(): self
    {
        if ($this->min === null && $this->max === null) {
            $negativePattern = "-?";
        } elseif ($this->min === null && $this->max<=0) {
            $negativePattern = "-";
        } elseif ($this->max === null && $this->min>=0) {
            $negativePattern = "";
        } else {
            $negativePattern = "-?";
        }
        $escapedMinus = preg_quote($negativePattern, '/');

        if ($this->dp <= 0) {
            $pattern = '^' . $escapedMinus . '\\d+$';
        }
        else {
            $pattern = '^' . $escapedMinus . '\\d+\\.\\d{' . $this->dp . '}$';
        }
        $this->input->set('pattern', $pattern);
        return $this;
    }

    public function hydrate(): void
    {
        if ($this->min !== null) {
            $this->min($this->min);
        }
        if ($this->max !== null) {
            $this->max($this->max);
        }
        if ($this->dp) {
            $this->dp($this->dp);
        }
        if ($this->strategy !== self::ROUND_STANDARD) {
            $this->roundStrategy($this->strategy);
        }
    }
}
