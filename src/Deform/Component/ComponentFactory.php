<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Util\Strings;

/**
 * component factory to ensure auto-completion support
 *
 * @method static Button Button(string $n, string $f, array $attributes=[])
 * @method static Checkbox Checkbox(string $n, string $f, array $attributes=[])
 * @method static CheckboxMulti CheckboxMulti(string $n, string $f, array $attributes=[])
 * @method static Currency Currency(string $n, string $f, array $attributes=[])
 * @method static Date Date(string $n, string $f, array $attributes=[])
 * @method static DateTime DateTime(string $n, string $f, array $attributes=[])
 * @method static Display Display(string $n, string $f, array $attributes=[])
 * @method static Email Email(string $n, string $f, array $attributes=[])
 * @method static Hidden Hidden(string $n, string $f, array $attributes=[])
 * @method static Input Input(string $n, string $f, array $attributes=[])
 * @method static InputButton InputButton(string $n, string $f, array $attributes=[])
 * @method static Password Password(string $n, string $f, array $attributes=[])
 * @method static RadioButtonSet RadioButtonSet(string $n, string $f, array $attributes=[])
 * @method static Select Select(string $n, string $f, array $attributes=[])
 * @method static SelectMulti SelectMulti(string $n, string $f, array $attributes=[])
 * @method static Submit Submit(string $n, string $f, array $attributes=[])
 * @method static TextArea TextArea(string $n, string $f, array $attributes=[])
 */
class ComponentFactory
{
    public const POUNDS = "&pound;";
    public const EUROS = "&euro;";
    public const AUSTRALIAN_DOLLARS = "A&dollar;";

    /** @var \ReflectionClass */
    private static $reflectionSelf;

    /** @var object[] */
    private static $components;

    /**
     * @param string $method
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public static function __callStatic(string $method, array $arguments)
    {
        self::$reflectionSelf = new \ReflectionClass(self::class);

        self::identifyComponents();

        if (!in_array($method, self::$components)) {
            throw new \Exception(
                "You are trying to construct a Component which hasn't been registered."
                . " Please add a suitable @method signature to the Component class for '" . $method . "'"
            );
        }

        $namespace = array_shift($arguments);
        $fieldName = array_shift($arguments);
        return self::build($method, $namespace, $fieldName, $arguments);
    }

    /**
     * @param string $component
     * @param string $namespace
     * @param string $fieldName
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public static function build(string $component, string $namespace, string $fieldName, array $arguments = [])
    {
        if (($namespaceDividerPos = strrpos($component, '\\')) !== false) {
            // if a namespace was included then check & strip it!
            $namespace = substr($component, 0, $namespaceDividerPos);
            if ($namespace !== __NAMESPACE__) {
                throw new \Exception(__METHOD__ . " can only accept classes in the namespace " . __NAMESPACE__);
            }
            $component = substr($component, $namespaceDividerPos + 1);
        }

        $class = __NAMESPACE__ . '\\' . $component;
        if (!class_exists($class)) {
            throw new \Exception("Failed to find class for component '" . $component . "' : " . $class);
        }

        // use the protected constructor!
        $reflectionClass = new \ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        $constructor->setAccessible(true);
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $constructor->invokeArgs($object, [
            $namespace,
            $fieldName,
            $arguments
        ]);
        return $object;

        //return new $class(...$arguments);
    }

    /**
     * @param string $componentName
     * @return bool
     * @throws \Exception
     */
    public static function isRegisteredComponent(string $componentName): bool
    {
        self::identifyComponents();
        return in_array($componentName, self::$components);
    }

    /**
     * analyses the phpdoc element from this class
     * @throws \Exception
     */
    private static function identifyComponents()
    {
        if (self::$components === null) {
            self::$components = [];
            if (!self::$reflectionSelf) {
                self::$reflectionSelf = new \ReflectionClass(self::class);
            }
            $comments = explode(PHP_EOL, self::$reflectionSelf->getDocComment());
            array_walk($comments, function ($comment) {
                $signature = Strings::extractStaticMethodSignature($comment);
                if ($signature) {
                    self::$components[] = $signature['methodName'];
                }
            });
        }
    }
}
