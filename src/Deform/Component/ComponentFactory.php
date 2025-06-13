<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Component\Shadow\Generator;
use Deform\Exception\DeformComponentException;
use Deform\Exception\DeformException;
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
 * @method static Decimal Decimal(string $namespace, string $field, array $attributes = [])
 * @method static Display Display(string $namespace, string $field, array $attributes=[])
 * @method static Email Email(string $namespace, string $field, array $attributes=[])
 * @method static File File(string $namespace, string $field, array $attributes=[])
 * @method static Image Image(string $namespace, string $field, array $attributes=[])
 * @method static \Deform\Component\Integer Integer(string $namespace, string $field, array $attributes = [])
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

    public static ?array $methodsByComponent = null;

    /**
     * @param string $method
     * @param array $arguments
     * @return object
     * @throws DeformException
     */
    public static function __callStatic(string $method, array $arguments)
    {
        self::identifyComponents();

        if (!in_array($method, self::$components)) {
            throw new DeformComponentException(
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
     * @throws DeformException
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
                throw new DeformComponentException(
                    __METHOD__ . " can only accept classes in the namespace " . __NAMESPACE__
                );
            }
            $component = substr($component, $namespaceDividerPos + 1);
        }

        $class = __NAMESPACE__ . '\\' . $component;
        if (!class_exists($class)) {
            throw new DeformComponentException("Failed to find class for component '" . $component . "' : " . $class);
        }
        if (!is_subclass_of($class, BaseComponent::class)) {
            throw new DeformComponentException("You can only build components (which must subclass BaseComponent)");
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
     * @throws DeformException
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
     * @throws DeformException
     */
    public static function isRegisteredComponent(string $componentName): bool
    {
        self::identifyComponents();
        return in_array($componentName, self::$components);
    }

    /**
     * @return string[]
     * @throws DeformException
     */
    public static function getRegisteredComponents(): array
    {
        self::identifyComponents();
        return self::$components;
    }

    /**
     * generate javascript definitions for the components
     * @param bool $compress
     * @return false|string
     * @throws DeformException
     */
    public static function getCustomElementDefinitionsJavascript(bool $compress = false): false|string
    {
        $setupJs = Generator::alterDeformObject();
        $componentNames = self::getRegisteredComponents();
        $js = [];
        $js[] = $compress ? Strings::trimInternal($setupJs) : $setupJs;
        foreach ($componentNames as $componentName) {
            $generator = new Generator($componentName);
            $componentJs = $generator->generateCustomComponentJavascript();
            $js[] = $compress ? Strings::trimInternal($componentJs) : $componentJs;
        }
        return implode(PHP_EOL, $js);
    }

    /**
     * analyses the phpdoc element from this class
     * @throws DeformException
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
