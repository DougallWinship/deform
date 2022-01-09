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
}
