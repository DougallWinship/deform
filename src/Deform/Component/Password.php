<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @method $this maxlength(int $maxLength)
 * @method $this minlength(int $maxLength)
 * @method $this pattern(int $pattern)
 * @method $this placeholder(string $text)
 * @method $this size(int $chars)
 */
class Password extends Input
{
    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('password');
    }
}
