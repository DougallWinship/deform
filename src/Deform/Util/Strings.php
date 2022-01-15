<?php

namespace Deform\Util;

/**
 * string handling utility functions
 */
class Strings
{
    /**
     * get a class name for an object or class name *without* it's namespace
     *
     * @param string|object $object
     * @return string
     * @throws \Exception
     */
    public static function getClassWithoutNamespace($object): string
    {
        if (is_object($object)) {
            $class_name = get_class($object);
        } elseif (is_string($object)) {
            $class_name = $object;
        } else {
            throw new \Exception("Parameter must be an object or class name");
        }

        $idx = strrpos($class_name, "\\");
        if ($idx === false) {
            return $class_name;
        }
        return substr($class_name, $idx + 1);
    }

    /**
     * Returns the given camelCasedWord as a character separated word, default is underscored_word
     *
     * @param string $camelCasedWord Camel-cased word
     * @param string $separator
     * @return string
     */
    public static function separateCased(string $camelCasedWord, string $separator = '_'): string
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', $separator . '\\1', $camelCasedWord));
    }
}
