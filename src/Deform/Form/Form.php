<?php

declare(strict_types=1);

namespace Deform\Form;

use Deform\Component\ComponentFactory;

class Form
{
    private string $formNamespace;

    public function __construct(string $formNamespace)
    {
        $this->formNamespace = $formNamespace;
    }

    public function generate(array $components, array $formAttributes = []): \Deform\Html\HtmlTag
    {
        $formHtml = \Deform\Html\Html::form($formAttributes);
        foreach ($components as $name => $componentDefinition) {
            $component = $this->buildComponent($name, $componentDefinition);
            $formHtml->add($component);
        }
        return $formHtml;
    }

    public function generateDOMDocument(array $components, array $formAttributes)
    {
        $formHtml = $this->generate($components, $formAttributes);
        return \Deform\Html\Html::getDOMDocument($formHtml);
    }

    private function buildComponent($name, $component)
    {
        if (is_object($component)) {
            if ($component instanceof \Deform\Component\BaseComponent) {
                return $component;
            } elseif ($component instanceof \Deform\Html\IHtml) {
                return $component;
            } else {
                throw new \Exception("Unexpected component class specified : " . get_class($component));
            }
        }
        if (is_string($component)) {
            if (class_exists($component)) {
                return ComponentFactory::build($component, $this->formNamespace, $name);
            } else {
                throw new \Exception("The specified component class was not found : " . $component);
            }
        }
    }

    public static function run($namespace, $components, $formProcessor = null)
    {
        $instance = new self($namespace);
        if (isset($_POST[$namespace])) {
            $formData = $_POST[$namespace];
            if (is_callable($formProcessor)) {
                $formProcessor($formData);
            }
        }
        return $instance->generate($components)
            ->set('id', 'form-' . $namespace)
            ->set('method', 'POST')
            ->set('action', '');
    }
}
