<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformException;
use Deform\Html\Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute acceptType
 */
class File extends Input
{
    use Shadow\File;

    public ?string $acceptType = null;

    protected HtmlTag $clearButton;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('file');
        $this->requiresMultiformEncoding = true;
    }

    /**
     * @param string $acceptType
     * @return static
     * @throws DeformException
     */
    public function accept(string $acceptType): static
    {
        $this->acceptType = $acceptType;
        $this->input->set('accept', $this->acceptType);
        return $this;
    }

    /**
     * @return HtmlTag
     * @throws DeformException
     */
    public function getHtmlTag(): HtmlTag
    {
        $htmlTag = parent::getHtmlTag();
        list($labelDiv) = $htmlTag->getChildren();
        $this->clearButton = Html::button([
            'class' => 'clear-button',
            'style' => 'line-height:10px;float:right',
            'onclick' => 'let input = this.parentNode.nextSibling.firstChild; input.value=null;'
        ])->add('clear');
        $labelDiv->set('onclick', 'return false');
        $labelDiv->prepend($this->clearButton);
        return $htmlTag;
    }

    /**
     * @return void
     * @throws DeformException
     */
    public function hydrate(): void
    {
        if ($this->acceptType != null) {
            $this->accept($this->acceptType);
        }
    }
}
