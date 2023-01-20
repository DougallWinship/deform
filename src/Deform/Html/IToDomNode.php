<?php

namespace Deform\Html;

interface IToDomNode
{
    public function getDomNode(\DOMDocument $domDocument);
}
