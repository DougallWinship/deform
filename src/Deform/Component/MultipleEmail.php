<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 */
class MultipleEmail extends Input
{
    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('email');
        $this->input->set('multiple', 'multiple');
    }

    /**
     * @param string|array $emails
     * @return self
     */
    public function emails(string|array $emails): self
    {
        if (is_array($emails)) {
            $emails = implode(",", $emails);
        }
        $this->input->value($emails);
        return $this;
    }
}
