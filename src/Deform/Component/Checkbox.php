<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformException;
use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @method Checkbox checked(string $checked)
 * @persistAttribute inputLabelText
 */
class Checkbox extends Input
{
    use \Deform\Component\Shadow\Checkbox;

    public ?string $inputLabelText = null;

    /** @var HtmlTag $inputLabel */
    public HtmlTag $inputLabel;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        $this->autoAddControl = false;
        parent::setup();
        $this->type('checkbox');
        $this->input->value('1'); // default ... it's easy to change but doesn't yet have hydration support
        $this->inputLabel = Html::label(['for' => $this->getId()])->add($this->fieldName);
        $this->addControl($this->input, [
            $this->input,
            ' ',
            $this->inputLabel,
        ]);
        $this->addExpectedField($this->fieldName);
    }

    /**
     * @param string $text
     * @return static
     * @throws DeformException
     */
    public function text(string $text): static
    {
        $this->inputLabelText = $text;
        $this->inputLabel->reset($text);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): static
    {
        if ($value) {
            $this->input->set('checked', 'checked');
        } else {
            $this->input->unset('checked');
        }
        return $this;
    }

    public function getHtmlTag(): HtmlTag
    {
        $tag = parent::getHtmlTag();
        $labelDiv = $tag->getChildren()[0];
        $label = $labelDiv->getChildren()[0];
        $label->unset('for');
        return $tag;
    }

    /**
     * @inheritDoc
     * @throws DeformException
     */
    public function hydrate(): void
    {
        if (is_string($this->inputLabelText)) {
            $this->text($this->inputLabelText);
        }
    }
}
