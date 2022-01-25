<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @method $this alt(string $altText)
 * @method $this height(int $pixels)
 * @method $this width(int $pixels)
 * @method $this src(string $submitButtonImageUrl)
 */
class Image extends Input
{
    public function setup()
    {
        parent::setup();
        $this->type('image');
        $this->requiresMultiformEncoding = true;
    }
}
