<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

class Attribute
{
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_ARRAY = 'array';
    public const TYPE_JSON_ARRAY = 'json';
    public const TYPE_FILE = 'file';
    public const SLOT_SELECTOR = 'slot';
    public const NAME_SELECTOR = 'name';

    public bool $dynamic = false;

    public function __construct(
        public string $name,
        public string $selector,
        public string $type,
        public string $initialiseJs = '',
        public string $updateJs = '',
        public bool $hideIfEmpty = true
    ){
        if ($updateJs) {
            $this->dynamic = true;
        }
    }

    public function metadata(): array
    {
        return [
            'name' => $this->name,
            'selector' => $this->selector,
            'type' => $this->type,
        ];
    }
}