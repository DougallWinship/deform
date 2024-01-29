<?php

namespace Deform\Html;

interface IHtml extends \Stringable, ISelectableNodes, IToDomNode
{
    public function set(string $name, $arguments): HtmlTag;
    public function setIfExists(string $name, $arguments): HtmlTag;
    public function setMany(array $attributes): HtmlTag;
}
