<?php

namespace Deform\Html;

/**
 * contract for an HtmlTag instance
 */
interface IHtml extends \Stringable, ISelectableNodes, IToDomNode
{
    public function set(string $name, $arguments): HtmlTag;
    public function setIfExists(string $name, $arguments): HtmlTag;
    public function setMany(array $attributes): HtmlTag;
}
