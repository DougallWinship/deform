<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I (via $this->tester)

use Deform\Component\BaseComponent;

class Unit extends \Codeception\Module
{
    public function getAttributeValue($object, $attribute, $checkParents=true)
    {
        $reflection = new \ReflectionClass($object);
        if (!$reflection->hasProperty($attribute) && $checkParents) {
            $parentClasses = class_parents($object);
            foreach ($parentClasses as $parentClass) {
                $reflection = new \ReflectionClass($parentClass);
                if ($reflection->hasProperty($attribute)) {
                    break;
                }
            }
        }
        $property = $reflection->getProperty($attribute);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    public function setAttributeValue($object, $attribute, $value)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($attribute);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public function instantiateClass(string $class, array $args): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        $constructor->setAccessible(true);
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $constructor->invokeArgs($object, $args);
        return $object;
    }
}
