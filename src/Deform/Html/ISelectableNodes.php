<?php

namespace Deform\Html;

interface ISelectableNodes
{
    public function findNodes(string $basicSelector): array;
}
