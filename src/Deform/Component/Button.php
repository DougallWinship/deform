<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @method Button value(string $value)
 * @persistAttribute buttonText
 */
class Button extends BaseComponent
{
    public string $buttonText;

    /**
     * @var IHtml input of type button
     */
    public $button;

    /**
     * @throws \Exception
     */
    public function setup()
    {
        $this->autolabel(false);
        $this->button = Html::button([
            "id" => $this->getId(),
            "name" => $this->getName()
        ]);
        $this->addControl($this->button);
    }

    /**
     * @param $text
     * @return $this
     * @throws \Exception
     */
    public function text($text)
    {
        $this->buttonText = $text;
        $this->button->reset($text);
        return $this;
    }
}
