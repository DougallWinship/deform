<?php

namespace Deform\Html;

/**
 * contract to obtain a DOMNode for a particular DOMDocument
 */
interface IToDomNode
{
    public function getDomNode(\DOMDocument $domDocument): \DOMNode;
}
