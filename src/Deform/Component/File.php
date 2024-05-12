<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @persistAttribute acceptType
 */
class File extends Input
{
    public ?string $acceptType = null;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('file');
        $this->requiresMultiformEncoding = true;
    }

    public function accept(string $acceptType)
    {
        $this->acceptType = $acceptType;
        $this->input->set('accept', $this->acceptType);
        return $this;
    }

    public function hydrate()
    {
        if ($this->acceptType != null) {
            $this->accept($this->acceptType);
        }
    }
}
