<?php

declare(strict_types=1);

namespace Deform\Form\DataSource\Database;

use Deform\Form\DataSource\DataMapper;

class Mysql extends DataMapper
{
    public function getComponentClassForDataType(string $type)
    {
    }
}
