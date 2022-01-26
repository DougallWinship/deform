<?php

namespace Deform\Html;

interface IToDomNode
{
    /** @var \DOMNode */
    public function getDomNode(\DOMDocument $domDocument);
}
