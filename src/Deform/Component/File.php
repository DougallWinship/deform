<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @method self accept(string $acceptType)
 */
class File extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->type('file');
        $this->requiresMultiformEncoding = true;
    }
}
