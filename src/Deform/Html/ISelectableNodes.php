<?php

namespace Deform\Html;

/**
 * contract for node searching using a basic selector
 */
interface ISelectableNodes
{
    public function findNodes(string $basicSelector): array;
}
