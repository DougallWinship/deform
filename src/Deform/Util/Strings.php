<?php

namespace Deform\Util;

use Deform\Exception\DeformException;
use Deform\Exception\DeformUtilException;

/**
 * string handling utilities
 */
class Strings
{
    /**
     * Get a class name for an object or class name *without* it's namespace.
     * @param string|object $object
     * @return string
     * @throws DeformException
     */
    public static function getClassWithoutNamespace(mixed $object): string
    {
        if (is_object($object)) {
            $className = get_class($object);
        } elseif (is_string($object)) {
            $className = $object;
        } else {
            throw new DeformUtilException("Parameter must be an object or class name");
        }

        if (!class_exists($className)) {
            throw new DeformUtilException("Class does not exist : " . $className);
        }

        $idx = strrpos($className, "\\");
        if ($idx === false) {
            return $className;
        }
        return substr($className, $idx + 1);
    }

    /**
     * Returns the given lower_case_and_underscored_word as a CamelCased word.
     * @param string $lowerCaseAndUnderscoredWord Word to camelize
     * @return string word in camel case : LikeThis.
     */
    public static function camelise(string $lowerCaseAndUnderscoredWord): string
    {
        $str = ucwords(str_replace(array('_', '-'), ' ', $lowerCaseAndUnderscoredWord));
        return str_replace(' ', '', $str);
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

    /**
     * Trim whitespace from start and end of a string and collapse any multiple internal whitespace to single spaces.
     * @param string $text
     * @return string
     */
    public static function trimInternal(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Extract the method name, if available, from a PHPDocs style annotation comment line (not particularly strict!).
     * @param string $comment
     * @return string[]|null
     */
    public static function extractStaticMethodSignature(string $comment): ?array
    {
        $trimmed = self::trimInternal($comment);
        if (!str_starts_with($trimmed, '* ')) {
            return null;
        }
        $trimmed = substr($trimmed, 2);
        $parts = explode(" ", $trimmed);
        if (count($parts) >= 4 && $parts[0] === '@method' && $parts[1] === 'static') {
            $result = preg_match('/(\S+)\s+(\S+)\s+(\S+)\s+(.*?)\((.*?)\)(\s*)(.*)/', $trimmed, $matches);
            if (!$result) {
                return null;
            }
            $signature = [];
            list(
                $discardAll,
                $discardMethod,
                $discardStatic,
                $signature['className'],
                $signature['methodName'],
                $signature['params'],
                $discardWhitespace,
                $signature['comment']
                ) = $matches;
            if ($signature['comment']) {
                $signature['comment_parts'] = explode(' ', $signature['comment']);
            }
            return $signature;
        }
        return null;
    }

    /**
     * Prepend a string to each line of another string.
     * @param string $str
     * @param string $prependStr
     * @return string
     */
    public static function prependPerLine(string $str, string $prependStr): string
    {
        $rebuild = [];
        $parts = explode(PHP_EOL, $str);
        foreach ($parts as $part) {
            $rebuild[] = $prependStr . $part;
        }
        return implode(PHP_EOL, $rebuild);
    }
}
