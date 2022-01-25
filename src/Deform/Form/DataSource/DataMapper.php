<?php

declare(strict_types=1);

namespace Deform\Form\DataSource;

abstract class DataMapper
{
    public function getComponentClassForDataType(string $type)
    {
    }
}
