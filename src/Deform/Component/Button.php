<?php
namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @method Button button(array $options)
 */
class Button extends BaseComponent
{
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
        $this->control($this->button);
    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value): Button
    {
        $this->button->set('value',$value);
        return $this;
    }

    public function text($text)
    {
        $this->button->reset($text);
        return $this;
    }

    public function beforeRender()
    {
        if ($this->button->isEmpty()) {
            $this->button->add($this->fieldName);
        }
    }
}
