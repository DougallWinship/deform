<?php

namespace Deform\Util;

/**
 * string handling utility functions
 */
class Strings
{
    /**
     * get a class name for an object or class name *without* it's namespace
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
     * Returns the given lower_case_and_underscored_word as a CamelCased word.
     * @param string $lowerCaseAndUnderscoredWord Word to camelize
     * @return string Camelised word : LikeThis.
     */
    public static function camelise(string $lowerCaseAndUnderscoredWord): string
    {
        $str = ucwords(str_replace(array('_', '-'), ' ', $lowerCaseAndUnderscoredWord));
        return str_replace(' ','', $str);
    }

    /**
     * Returns the given CamelCasedWord as a character separated word (default separator is underscore)
     * @param string $camelCasedWord Camel-cased word
     * @param string $separator
     * @return string Separated word : like_this.
     */
    public static function separateCased(string $camelCasedWord, string $separator = '_'): string
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', $separator . '\\1', $camelCasedWord));
    }
}
