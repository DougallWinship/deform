<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;

/**
 * @method self accept(string $acceptType)
 */
class MultipleFile extends File
{
    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->input->set('multiple', 'multiple');
        $list = Html::ul(['class' => 'filelist']);
        $this->componentContainer->control->addHtmlTag($list);
        $this->input->set('onchange', 'console.log("event",event);');
        $this->input->set('name', $this->input->get('name') . "[]");
    }
}
