<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @method Button value(string $value)
 * @persistAttribute buttonHtml
 * @persistAttribute buttonType
 */
class Button extends BaseComponent
{
    public const VALID_BUTTON_TYPES = ['submit', 'reset', 'button'];

    /** @var string */
    public string $buttonHtml;

    /** @var string */
    public string $buttonType = 'submit';

    /**
     * @var IHtml input of type button
     */
    public $button;

    /**
     * @inheritDoc
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
     * @param string $html
     * @return $this
     * @throws \Exception
     */
    public function html(string $html): self
    {
        $this->buttonHtml = $html;
        $this->button->reset($html);
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function type(string $type): self
    {
        $type = strtolower($type);
        if (!in_array($type, self::VALID_BUTTON_TYPES)) {
            throw new \Exception(
                "Invalid button type '" . $type . "', " .
                "valid are : " . implode(", ", self::VALID_BUTTON_TYPES)
            );
        }
        $this->buttonType = $type;
        $this->button->set('type', $type);
        return $this;
    }

    public function hydrate()
    {
        if ($this->buttonType) {
            $this->type($this->buttonType);
        }
        $this->html($this->buttonHtml);
    }
}
