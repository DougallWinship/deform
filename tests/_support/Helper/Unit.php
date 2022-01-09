<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I (via $this->tester)

class Unit extends \Codeception\Module
{
    public function getAttributeValue($object, $property)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
