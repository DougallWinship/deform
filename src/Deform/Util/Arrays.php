<?php
namespace Deform\Util;

class Arrays
{
    /**
     * check if the specified array is associative or not. in reality all php arrays are associative so what's really
     * being checked for is to see the keys are a numerical 0 based incrementing index.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * @param array $objectArray
     * @param string $getterName
     *
     * @return array
     * @throws \Exception
     */
    public static function sortByGetter(array $objectArray, string $getterName): array
    {
        $getter_values = [];
        foreach ($objectArray as $object) {
            if (!method_exists($object, $getterName)) {
                throw new \Exception("Object of type '" . get_class($object) . "' doesn't have a getter called '" . $getterName . "'");
            }
            $getter_values[] = $object->$getterName();
        }
        array_multisort($getter_values, $objectArray);
        return $objectArray;
    }

    /**
     * searches through an array and extracts keys and elements who's key ends with a specified string
     *
     * @param string $keyPostfix
     * @param array $array
     *
     * @return array
     */
    public static function getArrayElementsWithKeysEnding(string $keyPostfix, array $array): array
    {
        $build_array = [];
        foreach ($array as $key => $value) {
            if (Strings::endsWith($key, $keyPostfix)) {
                $build_array[$key] = $value;
            }
        }
        return $build_array;
    }

    /**
     * searches an array for a given sequence in its values.
     *
     * @param array $searchArray
     * @param array $sequenceArray sequence to search for (values are used for matching)
     * @param mixed $keepReadingUntil keep adding values after the sequence is initially found until this value is found
     * @param bool|int $limit maximum number of sequences to extract
     *
     * @return array contains each discovered sequence
     * @throws \Exception
     */
    public static function searchValuesForSequence(array $searchArray, array $sequenceArray, $keepReadingUntil = false, $limit = false): array
    {
        $sequence_index = 0;
        $results = [];
        $buildSequence = [];
        $matched = false;
        foreach ($searchArray as $key => $value) {
            if ($matched || $value == $sequenceArray[$sequence_index]) {
                if ($sequence_index == 0) {
                    $buildSequence = [];
                }
                $buildSequence[] = $value;
                $sequence_index++;
                if ($keepReadingUntil === false) {
                    if ($sequence_index == sizeof($sequenceArray)) {
                        $results[] = $buildSequence;
                        $sequence_index = 0;
                        if ($limit && sizeof($results) == $limit) {
                            return $results;
                        }
                    }
                } else {
                    if ($sequence_index == sizeof($sequenceArray)) {
                        $matched = true;
                    } elseif ($matched && $value == $keepReadingUntil) {
                        $results[] = $buildSequence;
                        $sequence_index = 0;
                        $matched = false;
                        if ($limit && sizeof($results) == $limit) {
                            return $results;
                        }
                    }
                }
            }
        }
        if ($matched) {
            $results[] = $buildSequence;
        }
        return $results;
    }

    /**
     * extract a array string array from an array of objects by requesting a method call on each object
     *
     * @param object[] $objectArray
     * @param string $getterName
     * @param null|callable $arrayMapFunction
     *
     * @return array
     * @throws \Exception
     */
    public static function extractArrayFromObjectsByGetter(array &$objectArray, string $getterName, callable $arrayMapFunction = null): array
    {
        $returnArray = [];
        foreach ($objectArray as $object) {
            if (!is_object($object))
                throw new \Exception("All items in the object_array must be objects");
            if (!method_exists($object, $getterName))
                throw new \Exception("All items in the object array must have the method '" . $getterName . "'");
            $returnArray[] = $object->$getterName();
        }
        if (is_callable($arrayMapFunction)) {
            $returnArray = array_map($arrayMapFunction, $returnArray);
        }
        return $returnArray;
    }

    /**
     * wrapper for usage of array_splice to insert a key=>value pair at the specific index in the array
     *
     * @param array $intoArray
     * @param int $injectIndex
     * @param string $injectKey
     * @param mixed $injectValue
     */
    public static function injectElementAtPosition(array &$intoArray, int $injectIndex, string $injectKey, &$injectValue)
    {
        array_splice($intoArray, $injectIndex, 0, [$injectKey => $injectValue]);
    }

    /**
     * Checks the given array for the given key and returns it. If not found returns default.
     *
     * @param string|int $key
     * @param array $array
     * @param null|mixed $default
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getValueOrDefault($key, array $array, $default = null)
    {
        if (!is_array($array)) {
            throw new \Exception('get_value_or_default expects an array');
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }

    /**
     * implode an array including its keys (mainly useful for html attributes)
     *
     * @param string[] $array
     * @param string $itemSeparator comes between the key-value pairs
     * @param string $keyValueSeparator comes between the keys and the values
     * @param string $valueWrapper wraps the value
     * @return string
     */
    public static function implodeWithKeys(array $array, string $itemSeparator = " ", string $keyValueSeparator = "=", string $valueWrapper = "'"): string
    {
        $attributes = array_map(function ($value, $key) use ($keyValueSeparator, $valueWrapper) {
            return $key . $keyValueSeparator . $valueWrapper . $value . $valueWrapper;
        }, array_values($array), array_keys($array));
        return implode($itemSeparator, $attributes);
    }

    /**
     * @param array $input_array
     * @param string $delimiter
     * @param null|callable $callback
     * @return string
     */
    public static function implodeWithFilter(array $input_array, string $delimiter = ', ', callable $callback = null): string
    {
        if (is_null($callback)) {
            return implode($delimiter, array_filter($input_array));
        }
        return implode($delimiter, array_filter($input_array, $callback));
    }

    /**
     * @param array $array
     * @param string $column
     * @return array
     */
    public static function reindexByColumn(array $array, string $column): array
    {
        return array_combine(array_column($array, $column), array_values($array));
    }

    /**
     * @param array $array
     * @param callable $callable
     * @return array
     */
    public static function reindexByCallable(array $array, callable $callable): array
    {
        return array_combine(array_map($callable, $array), array_values($array));
    }

    /**
     * similar to array_merge but explicitly doesn't monkey with the keys and complains if there's a key clash
     * @param array $array1
     * @param array $array2
     * @return array
     * @throws \Exception
     */
    public static function arrayMergeSafe(array $array1, array $array2): array
    {
        $intersectingKeys = array_intersect(array_keys($array1), array_keys($array2));
        if (count($intersectingKeys) > 0) {
            throw new \Exception("The following keys were found in both arrays '" . implode("','", $intersectingKeys) . "'");
        }
        return $array1 + $array2;
    }

    /**
     * similar to using implode but with a different final separator. useful to generate a string like this:
     *   'item1, item2, item3 and item4'
     *
     * @param array $elements
     * @param string $lastSeparator
     * @param string $separator
     * @param bool|string $getter - getter name if the array elements are objects
     *
     * @return string
     *@throws \Exception
     *
     */
    public static function makeList(array $elements, string $lastSeparator = ", and ", string $separator = ", ", $getter = false): string
    {
        if (is_string($getter)) {
            $elements = arrays::extractArrayFromObjectsByGetter($elements, $getter);
        }
        if (count($elements) == 0) {
            return "";
        }
        if (count($elements) == 1) {
            return $elements[0];
        }
        if (count($elements) == 2) {
            return $elements[0] . $lastSeparator . $elements[1];
        }
        $last_element = array_pop($elements);
        return implode($separator, $elements) . $lastSeparator . $last_element;
    }

    /**
     * @param array $array
     * @return int
     */
    function getArrayDepth(array $array): int
    {
        $depth = 0;
        $iteIte = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));

        foreach ($iteIte as $ite) {
            $d = $ite->getDepth();
            $depth = $d > $depth ? $d : $depth;
        }

        return $depth;
    }
}
