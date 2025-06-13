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
        if (is_string($emails)) {
            $emails = explode(',', $emails);
        }
        array_walk($emails, function (&$value) {
            $value = trim($value);
        });
        $emails = implode(",", $emails);
        $this->input->value($emails);
        return $this;
    }
}
