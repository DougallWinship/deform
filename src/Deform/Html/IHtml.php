<?php

namespace Deform\Html;

use Deform\Util\IToString;

interface IHtml extends IToString, ISelectableNodes, IToDomNode
{
    public function set(string $name, $arguments): HtmlTag;
    public function setIfExists(string $name, $arguments): HtmlTag;
    public function setMany(array $attributes): HtmlTag;
}
