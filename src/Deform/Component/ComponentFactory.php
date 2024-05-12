<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;
use Deform\Html\HtmlTag;
use Deform\Util\Strings;

/**
 * @method static Button Button(string $namespace, string $field, array $attributes=[])
 * @method static Checkbox Checkbox(string $namespace, string $field, array $attributes=[])
 * @method static CheckboxMulti CheckboxMulti(string $namespace, string $field, array $attributes=[])
 * @method static ColorSelector ColorSelector(string $namespace, string $field, array $attributes=[])
 * @method static Currency Currency(string $namespace, string $field, array $attributes=[])
 * @method static Date Date(string $namespace, string $field, array $attributes=[])
 * @method static DateTime DateTime(string $namespace, string $field, array $attributes=[])
 * @method static Display Display(string $namespace, string $field, array $attributes=[])
 * @method static Email Email(string $namespace, string $field, array $attributes=[])
 * @method static File File(string $namespace, string $field, array $attributes=[])
 * @method static Image Image(string $namespace, string $field, array $attributes=[])
 * @method static MultipleFile MultipleFile(string $namespace, string $field, array $attributes=[])
 * @method static MultipleEmail MultipleEmail(string $namespace, string $field, array $attributes=[])
 * @method static Hidden Hidden(string $namespace, string $field, array $attributes=[])
 * @method static Password Password(string $namespace, string $field, array $attributes=[])
 * @method static RadioButtonSet RadioButtonSet(string $namespace, string $field, array $attributes=[])
 * @method static Select Select(string $namespace, string $field, array $attributes=[])
 * @method static SelectMulti SelectMulti(string $namespace, string $field, array $attributes=[])
 * @method static Slider Slider(string $namespace, string $field, array $attributes=[])
 * @method static Submit Submit(string $namespace, string $field, array $attributes=[])
 * @method static Text Text(string $namespace, string $field, array $attributes=[])
 * @method static TextArea TextArea(string $namespace, string $field, array $attributes=[])
 */
class ComponentFactory
{
    public const POUNDS = "&pound;";
    public const EUROS = "&euro;";
    public const AUSTRALIAN_DOLLARS = "A&dollar;";

    /** @var \ReflectionClass|null */
    private static ?\ReflectionClass $reflectionSelf = null;

    /** @var string[]|null */
    public static ?array $components = null;

    /**
     * @param string $method
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public static function __callStatic(string $method, array $arguments)
    {
        self::identifyComponents();

        if (!in_array($method, self::$components)) {
            throw new \Exception(
                "You are trying to construct a Component which hasn't been registered."
                . " Please add a suitable @method signature to the Component class for '" . $method . "'"
            );
        }

        $namespace = array_shift($arguments);
        $fieldName = array_shift($arguments);
        $attributes = array_shift($arguments);
        return call_user_func([get_called_class(),'build'], $method, $namespace, $fieldName, $attributes ?? []);
    }

    /**
     * @param string $component
     * @param string|null $formNamespace
     * @param string $fieldName
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public static function build(
        string $component,
        ?string $formNamespace,
        string $fieldName,
        array $arguments = []
    ): object {
        if (($namespaceDividerPos = strrpos($component, '\\')) !== false) {
            // if a namespace was included then check & strip it!
            $checkNamespace = substr($component, 0, $namespaceDividerPos);
            if ($checkNamespace !== __NAMESPACE__) {
                throw new \Exception(__METHOD__ . " can only accept classes in the namespace " . __NAMESPACE__);
            }
            $component = substr($component, $namespaceDividerPos + 1);
        }

        $class = __NAMESPACE__ . '\\' . $component;
        if (!class_exists($class)) {
            throw new \Exception("Failed to find class for component '" . $component . "' : " . $class);
        }
        if (!is_subclass_of($class, BaseComponent::class)) {
            throw new \Exception("You can only build components (which must subclass BaseComponent)");
        }

        // use the protected constructor!
        $reflectionClass = new \ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        $constructor->setAccessible(true);
        /** @var BaseComponent $object it's not actually this, but BaseComponent *is* the parent */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $constructor->invokeArgs($object, [
            $formNamespace,
            $fieldName,
            $arguments
        ]);
        return $object;
    }

    /**
     * @param string $component
     * @return HtmlTag
     * @throws \Exception
     */
    public static function buildTemplate(string $component): HtmlTag
    {
        $lowerName = strtolower($component);
        $htmlTag = self::build($component, 'template', $lowerName);
        return Html::div()->id('template-' . $lowerName)->add($htmlTag);
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
     * @return string[]
     * @throws \Exception
     */
    public static function getRegisteredComponents(): array
    {
        self::identifyComponents();
        return self::$components;
    }

    /**
     * generate javascript definitions for the components
     * @return false|string
     * @throws \Exception
     */
    public static function getCustomElementDefinitionsJavascript(): false|string
    {
        $componentNames = self::getRegisteredComponents();
        ob_start();
        require(__DIR__ . DIRECTORY_SEPARATOR . '/Shadow/custom-element-definitions.php');
        return ob_get_clean();
    }

    /**
     * analyses the phpdoc element from this class
     * @throws \Exception
     */
    private static function identifyComponents(): void
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
