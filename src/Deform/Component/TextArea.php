<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

class TextArea extends BaseComponent
{
    /** @var HtmlTag */
    public $textarea;

    /**
     * @inheritDoc
     */
    public function setup()
    {
        $this->textarea = Html::textarea([
           'id' => $this->getId(),
           'name' => $this->getName()
        ]);
        $this->addControl($this->textarea);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): self
    {
        $this->textarea->reset($value);
        return $this;
    }

    public function hydrate()
    {
    }

    public function shadowJavascript(): array
    {
        return [
            '.control-container textarea' => <<<JS
setTimeout(()=> { this.textarea.textContent = this.textContent; })
JS
        ] + parent::shadowJavascript();
    }

    public function shadowJavascriptProperties(): array
    {
        return ['textarea' => "this.template.querySelector('textarea');"];
    }
}
