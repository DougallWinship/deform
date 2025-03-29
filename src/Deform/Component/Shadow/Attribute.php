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
    public const TYPE_KEYVALUE_ARRAY = 'keyvalue-array';
    public const TYPE_FILE = 'file';
    public const SLOT_SELECTOR = 'slot';
    public const NAME_SELECTOR = 'name';

    public const BEHAVIOUR_VISIBLE_IF_EMPTY = 'visible-if-empty';
    public const BEHAVIOUR_HIDE_IF_EMPTY = 'hide-if-empty';
    public const BEHAVIOUR_CUSTOM = 'custom';

    public bool $dynamic = false;

    public function __construct(
        public string $name,
        public string $selector,
        public string $type,
        public string $initialiseJs = '',
        public string $updateJs = '',
        public string $behaviour = self::BEHAVIOUR_HIDE_IF_EMPTY,
        public ?string $default = null
    ) {
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
            'default' => $this->default,
        ];
    }
}
