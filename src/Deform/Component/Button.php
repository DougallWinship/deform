<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformComponentException;
use Deform\Exception\DeformException;
use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @method Button value(string $value)
 * @persistAttribute buttonHtml
 * @persistAttribute buttonType
 */
class Button extends BaseComponent
{
    use Shadow\Button;

    public const array VALID_BUTTON_TYPES = ['submit', 'reset', 'button'];

    /** @var string */
    public string $buttonHtml;

    /** @var string */
    public string $buttonType = 'submit';

    /**
     * @var IHtml input of type button
     */
    public IHtml $button;

    /**
     * @inheritDoc
     */
    public function setup(): void
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
     * @return static
     * @throws DeformException
     */
    public function html(string $html): static
    {
        $this->buttonHtml = $html;
        $this->button->reset($html);
        return $this;
    }

    /**
     * @param string $type
     * @return static
     * @throws DeformException
     */
    public function type(string $type): static
    {
        $type = strtolower($type);
        if (!in_array($type, self::VALID_BUTTON_TYPES)) {
            throw new DeformComponentException(
                "Invalid button type '" . $type . "', " .
                "valid are : " . implode(", ", self::VALID_BUTTON_TYPES)
            );
        }
        $this->buttonType = $type;
        $this->button->set('type', $type);
        return $this;
    }

    /**
     * @inheritdoc
     * @throws DeformException
     */
    public function hydrate(): void
    {
        if ($this->buttonType) {
            $this->type($this->buttonType);
        }
        $this->html($this->buttonHtml);
    }
}
