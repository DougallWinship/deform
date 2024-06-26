<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

class TextArea extends BaseComponent
{
    use Shadow\TextArea;

    /** @var HtmlTag */
    public HtmlTag $textarea;

    /**
     * @inheritDoc
     */
    public function setup(): void
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
    public function setValue($value): static
    {
        $this->textarea->reset($value);
        return $this;
    }
}
