<?php
namespace Deform\Form\Model;

use Deform\Component\BaseComponent;
use \Deform\Component\ComponentFactory as Component;
use Deform\Html\Html;
use Deform\Html\HtmlTag;

class FormModel
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const HTML_KEY = "HTML:";

    private $htmlCounter = 1;

    private $namespace;
//    private $formAttributes = [
//        'action' => '',
//        'method' => self::METHOD_GET,
//    ];
    private $sections = [];// sections are either Components or HtmlTags
    private $formData;
    private $formAction;
    private $formMethod;

    public function __construct(string $namespace, string $formMethod, string $formAction='')
    {
        $this->namespace = $namespace;
        $this->formMethod = $formMethod;
        $this->formAction = $formAction;
    }

    public function addEmail($field, $options=[])
    {
        $this->sections[$field] = Component::Email($this->namespace, $field, $options);
    }

    public function addPassword($field, $options=[])
    {
        $this->sections[$field] = Component::Password($this->namespace, $field);
    }

    public function addSubmit($field)
    {
        $this->sections[$field] = Component::Submit($this->namespace, $field);
    }

    public function addHtml($html)
    {
        if (is_string($html)) {
            $html = Html::loadHtml($html);
        }
        if (!($html instanceof HtmlTag)) {
            throw new \Exception("Add HTML either as a string or as an HtmlTag");
        }
        $this->sections[self::HTML_KEY.($this->htmlCounter++)] = $html;
    }

    public function getFormHtml() : HtmlTag
    {
        $formHtml = Html::form([
            'method'=>$this->formMethod,
            'action'=>$this->formAction
        ]);
        foreach ($this->sections as $section) {
            $formHtml->add($section);
        }
        return $formHtml;
    }

    public function getFormDOMDocument() : \DOMDocument
    {
        $formHtml = $this->getFormHtml();
        return Html::getDOMDocument($formHtml);
    }

    public function process($data=null)
    {
        if (!self::isSubmitted($this->formMethod)) return false;

        if ($this->populateFormData($data)) {
            if ($this->validateFormData()) {
                $this->processFormData();
            }
            // if the form didn't validate it was still processed!
            return true;
        }
        else {
            return false;
        }
    }

    protected function populateFormData($data=null)
    {
        if ($data===null) {
            $rawData = self::getFormData($this->formMethod);
            if (!isset($rawData[$this->namespace])) {
                return false;
            }
            $this->formData = $rawData[$this->namespace];
        }
        else {
            $this->formData = $data;
        }
        return true;
    }

    protected function validateFormData()
    {
        return true;
    }

    protected function processFormData()
    {
        echo "<pre>".print_r($this->formData,true)."</pre>";
    }

    protected static function getFormData($method)
    {
        switch(strtolower($method)) {
            case self::METHOD_GET: return $_GET;
            case self::METHOD_POST: return $_POST;
            default:
                throw new \Exception("Unrecognised form method '".$method."'");
        }
    }

    protected static function isSubmitted($method)
    {
        switch(strtolower($method)) {
            case self::METHOD_GET: return $_GET;
            case self::METHOD_POST: return  $_SERVER['REQUEST_METHOD']=='POST';
            default:
                throw new \Exception("Unrecognised form method '".$method."'");
        }
    }

    public function getFieldComponent($field)
    {
        return $this->sections[$field];
    }

    public function toArray() : array
    {
        $formDefinition = [
            'tag' => 'form',
            'namespace' => $this->namespace,
            'action' => $this->formAction,
            'method' => $this->formMethod,
        ];
        $sectionsArray = [];
        foreach ($this->sections as $name=>$section) {
            if (strpos($name, self::HTML_KEY)===0) {
                $sectionsArray[] = [
                    'html' => (string)$section
                ];
            }
            elseif ($section instanceof BaseComponent) {
                $sectionsArray[] = $section->toArray();
            }
        }
        $formDefinition['sections']=$sectionsArray;
        return $formDefinition;
    }
}
