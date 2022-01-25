<?php

declare(strict_types=1);

namespace Deform\Form\Validation;

/**
 * defines a validation rule
 *
 * where possible this should include both a server and client side solution
 *
 * in some cases a joint solution is necessary (i.e. ajax for checking a field is unique, etc)
 * ... in this case suitable routing must be provided to the server side validate method
 */
abstract class ValidationRule
{
    /**
     * @param $value
     * @return bool|array true indicates successful validation, or else an error of failure messages
     */
    public function validate($value)
    {
    }

    /**
     * either a snippet of javascript or else a
     * @param $jsStrategy
     */
    public static function javascriptStrategy($jsStrategy)
    {
    }
}
