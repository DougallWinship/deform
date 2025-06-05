<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

use Deform\Exception\DeformComponentException;

class Attribute
{
    public const bool ADD_MISSING_SEMICOLONS = false;

    public const string TYPE_STRING = 'string';
    public const string TYPE_INTEGER = 'integer';
    public const string TYPE_FLOAT = 'float';
    public const string TYPE_BOOLEAN = 'boolean';
    public const string TYPE_ARRAY = 'array';
    public const string TYPE_KEYVALUE_ARRAY = 'keyvalue-array';
    public const string TYPE_FILE = 'file';
    public const string TYPE_TEXTAREA = 'textarea';

    public const string SLOT_SELECTOR = 'slot';
    public const string NAME_SELECTOR = 'name';

    public const string BEHAVIOUR_VISIBLE_IF_EMPTY = 'visible-if-empty';
    public const string BEHAVIOUR_HIDE_IF_EMPTY = 'hide-if-empty';
    public const string BEHAVIOUR_CUSTOM = 'custom';

    public bool $dynamic = false;

    public function __construct(
        public string $name,
        public string $selector,
        public string $type,
        public string $initialiseJs = '',
        public string $updateJs = '',
        public string $behaviour = self::BEHAVIOUR_HIDE_IF_EMPTY,
        public ?string $default = null,
        public ?array $options = null,
    ) {
        if ($updateJs) {
            $this->dynamic = true;
        }
        if (self::ADD_MISSING_SEMICOLONS) {
            // paranoia ... get it right
            if (!str_ends_with($this->initialiseJs, ';')) {
                $this->initialiseJs .= ';';
            }
            if (!str_ends_with($this->updateJs, ';')) {
                $this->updateJs .= ';';
            }
        }
        if ($options !== null && $default !== null && !in_array($default, $options)) {
            throw new DeformComponentException(
                "The specified default '" . $default . "' is not in the array " . print_r($options, true)
            );
        }
    }

    public function metadata(): array
    {
        return [
            'name' => $this->name,
            'selector' => $this->selector,
            'type' => $this->type,
            'default' => $this->default,
            'options' => $this->options,
        ];
    }
}
