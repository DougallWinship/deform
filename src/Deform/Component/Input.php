<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @method Input autocomplete(bool $onOff)
 * @method Input autofocus(bool $onOff)
 * @method Input dirname(string $dir)
 * @method Input disabled(bool $onOff)
 * @method Input readonly(bool $readonly)
 * @method Input required(bool $required)
 * @method Input value(int|string $value)
 */
abstract class Input extends BaseComponent
{
    public bool $autoAddControl = true;

    /** @var HtmlTag */
    public HtmlTag $input;

    /**
     * @throws \Exception
     */
    public function setup()
    {
        $this->input = Html::input([
            'id' => $this->getId(),
            'name' => $this->getName(),
            'autocomplete' => 'off'
        ]);
        if ($this->autoAddControl) {
            $this->addControl($this->input);
        }
    }

    /**
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function type(string $type)
    {
        $this->input->set('type', $type);
        return $this;
    }
}
