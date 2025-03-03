<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @persistAttribute acceptType
 */
class File extends Input
{
    use Shadow\File;

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

    /**
     * @param string $acceptType
     * @return static
     * @throws \Exception
     */
    public function accept(string $acceptType): static
    {
        $this->acceptType = $acceptType;
        $this->input->set('accept', $this->acceptType);
        return $this;
    }

    public function hydrate(): void
    {
        if ($this->acceptType != null) {
            $this->accept($this->acceptType);
        }
    }
}
