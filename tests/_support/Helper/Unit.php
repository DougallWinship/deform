<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I (via $this->tester)

use Deform\Component\BaseComponent;
use Deform\Html\HtmlTag;

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

    public function callStaticMethod(string $class, string $method, array $args=[])
    {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs(null, $args);
    }

    public function assertArrayHasKeys(array $keys,array $array)
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    public function assertArrayContains(array $subset,array $array)
    {
        foreach ($subset as $key=>$value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($value, $array[$key]);
        }
    }

    public function assertIsHtmlTag($thing, $type, $hasAttributes=[])
    {
        $this->assertInstanceOf(HtmlTag::class, $thing);
        $tagName = $this->getAttributeValue($thing, 'tagName');
        $this->assertEquals($type, $tagName);
        $attributes = $this->getAttributeValue($thing,'attributes');
        if ($hasAttributes) {
            $this->assertArrayContains($hasAttributes, $attributes);
        }
    }
}

