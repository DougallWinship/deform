<?php

namespace Deform\Util;

class Arrays
{
    /**
     * check if the specified array is associative or not. in reality all php arrays are associative so what's really
     * being checked for is to see the keys are a numerical 0 based incrementing index.
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * extracts the specified set of keys from an array
     *
     * if $fields is non-associative the specified fields will be extracted (identical to Arr::only)
     * if $fields is associative then the keys will be extracted and remapped using the values
     *
     * @param array $data
     * @param array $keys
     * @param bool $strict if this is true then an exception is throw if any keys are not found
     * @return array
     * @throws \Exception
     */
    public static function extractKeys(array $data, array $keys, bool $strict = false): array
    {
        $isAssoc = self::isAssoc($keys);

        // this array contains what needs to be extracted as the keys (not the values)
        // to prepare for array_intersect_key
        $extractKeys = $isAssoc
            ? $keys
            : array_flip($keys);

        $results = array_intersect_key($data, $extractKeys);
        $missingKeys = array_flip(array_diff_key($extractKeys, $results));
        if ($strict && count($missingKeys) > 0) {
            throw new \Exception(
                "The following fields were specified which were not found in the data : "
                . implode(", ", $missingKeys)
            );
        }

        if (!$isAssoc && count($missingKeys) > 0) {
            foreach ($missingKeys as $missingKey) {
                $results[$missingKey] = null;
            }
            return $results;
        }

        if (!$isAssoc) {
            $keys = array_combine($keys, $keys);
        }

        $newResults = [];
        foreach ($keys as $oldKey => $newKey) {
            $newResults[$newKey] = array_key_exists($oldKey, $results) ? $results[$oldKey] : null;
        }
        return $newResults;
    }
}
