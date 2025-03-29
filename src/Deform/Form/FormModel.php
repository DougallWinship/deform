<?php

declare(strict_types=1);

namespace Deform\Form;

use Deform\Component\BaseComponent;
use Deform\Component\ComponentFactory;
use Deform\Html\Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;
use Deform\Util\Arrays;

/**
 * @method \Deform\Component\Button addButton(string $field, array $options=[])
 * @method \Deform\Component\Checkbox addCheckbox(string $field, array $options=[])
 * @method \Deform\Component\CheckboxMulti addCheckboxMulti(string $field, array $options=[])
 * @method \Deform\Component\ColorSelector addColorSelector(string $field, array $options=[])
 * @method \Deform\Component\Currency addCurrency(string $field, array $options=[])
 * @method \Deform\Component\Date addDate(string $field, array $options=[])
 * @method \Deform\Component\DateTime addDateTime(string $field, array $options=[])
 * @method \Deform\Component\Display addDisplay(string $field, array $options=[])
 * @method \Deform\Component\Email addEmail(string $field, array $options=[])
 * @method \Deform\Component\File addFile(string $field, array $options=[])
 * @method \Deform\Component\Hidden addHidden(string $field, array $options=[])
 * @method \Deform\Component\Image addImage(string $field, array $options=[])
 * @method \Deform\Component\MultipleEmail addMultipleEmail(string $field, array $options=[])
 * @method \Deform\Component\MultipleFile addMultipleFile(string $field, array $options=[])
 * @method \Deform\Component\Password addPassword(string $field, array $options=[])
 * @method \Deform\Component\RadioButtonSet addRadioButtonSet(string $field, array $options=[])
 * @method \Deform\Component\Select addSelect(string $field, array $options=[])
 * @method \Deform\Component\SelectMulti addSelectMulti(string $field, array $options=[])
 * @method \Deform\Component\Slider addSlider(string $field, array $options=[])
 * @method \Deform\Component\Submit addSubmit(string $field, array $options=[])
 * @method \Deform\Component\Text addText(string $field, array $options=[])
 * @method \Deform\Component\TextArea addTextArea(string $field, array $options=[])
 */
class FormModel
{
    public const string METHOD_GET = 'get';
    public const string METHOD_POST = 'post';
    public const string HTML_KEY = "HTML:";

    public const string ENCTYPE_MULTIPART_URL_ENCODED = "application/x-www-form-urlencoded";
    public const string ENCTYPE_MULTIPART_FORM_DATA = "multipart/form-data";

    public const string CSRF_STRATEGY_OFF = 'off';
    public const string CSRF_STRATEGY_SESSION = 'session';
    public const string CSRF_STRATEGY_COOKIE = 'cookie';

    public const string CSRF_TOKEN_FIELD = 'csrf-token';

    /** @var int */
    private int $htmlCounter = 1;

    /** @var string */
    private string $namespace;

    /** @var array */
    private array $sections = [];

    /** @var BaseComponent[] */
    private array $fieldComponents = [];

    /** @var string */
    private string $formAction;

    /** @var string */
    private string $formMethod;

    /** @var string */
    private string $autoComplete;

    /** @var string */
    private string $csrfStrategy = self::CSRF_STRATEGY_SESSION;

    /** @var string */
    private string $encType = self::ENCTYPE_MULTIPART_URL_ENCODED;

    /**
     * @param string $namespace
     * @param string $formMethod
     * @param string $formAction
     * @param string $autoComplete
     * @throws \Exception
     */
    public function __construct(
        string $namespace,
        string $formMethod = self::METHOD_POST,
        string $formAction = '',
        string $autoComplete = 'off'
    ) {
        if ($formMethod !== self::METHOD_POST && $formMethod !== self::METHOD_GET) {
            throw new \Exception("The form method must be either 'get' or 'post'");
        }
        $this->namespace = $namespace;
        $this->formMethod = $formMethod;
        $this->formAction = $formAction;
        $this->autoComplete = $autoComplete;
    }

    /**
     * @param $name
     * @param $arguments
     * @return BaseComponent|object
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (
            str_starts_with($name, 'add')
            && strlen($name) > 3
            && count($arguments) > 0
        ) {
            $componentName = substr($name, 3);
            if (!ComponentFactory::isRegisteredComponent($componentName)) {
                throw new \Exception(
                    "There is no component named '" . $componentName . "' registered in ComponentFactory"
                );
            }
            $field = $arguments[0];
            $options = $arguments[1] ?? [];
            if (!is_string($field)) {
                throw new \Exception("Unexpected type " . gettype($arguments[0]) . " for 'field' argument");
            }
            if (!is_array($options)) {
                throw new \Exception("Unexpected type " . gettype($arguments[1]) . " for 'options' argument");
            }
            if (isset($this->fieldComponents[$field])) {
                throw new \Exception("Field '" . $field . "' has already been defined");
            }
            $component = ComponentFactory::build(
                $componentName,
                $this->namespace,
                $field,
                $options
            );
            $this->sections[$field] = $component;
            $this->fieldComponents[$field] = $component;
            return $component;
        }
        throw new \BadMethodCallException("Call to undefined method " . __CLASS__ . "::" . $name . "()");
    }

    /**
     * @param string $url
     * @param string $text
     * @return void
     * @throws \Exception
     */
    public function addCancelLink(string $url, string $text = "Cancel"): void
    {
        $this->addHtml(Html::div(['class' => 'form-cancel-button'])
            ->add(Html::a(['href' => $url])->add($text)));
    }

    /**
     * @param \Stringable|string $html
     * @throws \Exception
     */
    public function addHtml(\Stringable|string $html): void
    {
        if ($html instanceof \Stringable) {
            $html = (string)$html;
        }
        if (!is_string($html)) {
            throw new \Exception("Add HTML either as a string or as an HtmlTag");
        }
        $this->sections[self::HTML_KEY . ($this->htmlCounter++)] = $html;
    }

    /**
     * @param bool $disableCsrf
     * @return HtmlTag
     * @throws \Exception
     */
    public function getFormHtml(bool $disableCsrf = false): HtmlTag
    {
        $formAttributes = [
            'method' => $this->formMethod,
            'action' => $this->formAction,
            'autocomplete' => $this->autoComplete
        ];
        foreach ($this->sections as $section) {
            if (
                ($section instanceof BaseComponent) &&
                ($this->encType === self::ENCTYPE_MULTIPART_URL_ENCODED) &&
                ($section->requiresMultiformEncoding())
            ) {
                $formAttributes['enctype'] = self::ENCTYPE_MULTIPART_FORM_DATA;
                $this->encType = self::ENCTYPE_MULTIPART_FORM_DATA;
            }
        }
        $formHtml = Html::form($formAttributes);

        foreach ($this->sections as $key => $section) {
            if ($section instanceof BaseComponent) {
                $sectionTag = $section->getHtmlTag();
                $formHtml->add($sectionTag);
            } elseif (is_string($section) || $section instanceof IHtml) {
                $formHtml->add($section);
            } else {
                throw new \Exception("Unexpected section type " . gettype($section) . " for section '" . $key . "'");
            }
        }
        if (!$disableCsrf) {
            $token = $this->implementCSRFStrategy();
            if ($token !== null) {
                $formHtml->add($token);
            }
        }
        return $formHtml;
    }

    /**
     * @return HtmlTag
     * @throws \Exception
     */
    public function run(): HtmlTag
    {
        if ($this->isSubmitted()) {
            $formData = $this->getFormData();
            if (!$this->validateCSRFToken($formData)) {
                $this->handleCSRFTokenFailure();
            }
            if ($this->populateFormData($formData)) {
                $validationResult = $this->validateFormData($formData);
                if ($validationResult === true) {
                    $this->processFormData($formData);
                } elseif (is_array($validationResult)) {
                    $this->setErrors($validationResult);
                } else {
                    throw new \Exception("Unexpected validation result must be true or an array");
                }
            }
        }
        return $this->getFormHtml();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isSubmitted(): bool
    {
        switch ($this->formMethod) {
            case self::METHOD_GET:
                return isset($_GET[$this->namespace]) && count($_GET[$this->namespace]) > 0;
            case self::METHOD_POST:
                return $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$this->namespace]);
            default:
                throw new \Exception("Unrecognised form method '" . $this->formMethod . "'");
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormData(): array
    {
        switch ($this->formMethod) {
            case self::METHOD_GET:
                return $_GET[$this->namespace];
            case self::METHOD_POST:
                return $_POST[$this->namespace];
            default:
                throw new \Exception("Unrecognised form method '" . $this->formMethod . "'");
        }
    }

    /**
     * extracts the data and populates the form
     * @param array $formData
     * @param bool $exceptionIfMissing throw an exception if there was not a corresponding component, otherwise ignored
     * @return array data without any form specific details (e.g. expected data fields & csrf token)
     * @throws \Exception
     */
    protected function populateFormData(array $formData, bool $exceptionIfMissing = true): array
    {
        if (isset($formData[BaseComponent::EXPECTED_DATA_FIELD])) {
            foreach ($formData[BaseComponent::EXPECTED_DATA_FIELD] as $expectedDatum) {
                if (!isset($formData[$expectedDatum])) {
                    $formData[$expectedDatum] = false;
                }
            }
            unset($formData[BaseComponent::EXPECTED_DATA_FIELD]);
        }
        if (isset($formData[self::CSRF_TOKEN_FIELD])) {
            unset($formData[self::CSRF_TOKEN_FIELD]);
        }
        foreach ($formData as $field => $value) {
            if (!isset($this->sections[$field])) {
                if ($exceptionIfMissing) {
                    throw new \Exception("No component found for '" . $field . "'");
                }
            } else {
                $fieldComponent = $this->fieldComponents[$field];
                $fieldComponent->setValue($value);
            }
        }
        return $formData;
    }

    /**
     * @param array $formData
     * @return true|array
     */
    protected function validateFormData(array $formData): true|array
    {
        return true;
    }

    /**
     * @param array $formData
     * @return void
     */
    public function processFormData(array $formData): void
    {
    }

    /**
     * @param array $errors
     * @throws \Exception
     */
    protected function setErrors(array $errors): void
    {
        foreach ($errors as $field => $error) {
            if (!isset($this->sections[$field])) {
                throw new \Exception("No component found for '" . $field . "'");
            }
            $fieldComponent = $this->fieldComponents[$field];
            $fieldComponent->setError($error);
        }
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getFieldComponent(string $field): mixed
    {
        return $this->sections[$field];
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        $formDefinition = [
            'tag' => 'form',
            'namespace' => $this->namespace,
            'action' => $this->formAction,
            'method' => $this->formMethod,
            'csrf' => $this->csrfStrategy,
        ];
        $sectionsArray = [];
        foreach ($this->sections as $name => $section) {
            if ($name === self::CSRF_TOKEN_FIELD) {
                continue;
            }
            if (str_starts_with($name, self::HTML_KEY)) {
                $sectionsArray[] = [
                    'html' => (string)$section
                ];
            } elseif ($section instanceof BaseComponent) {
                $sectionsArray[] = $section->toArray();
            }
        }
        $formDefinition['sections'] = $sectionsArray;
        return $formDefinition;
    }

    /**
     * @param array $definition
     * @return FormModel
     * @throws \Exception
     */
    public static function buildForm(array $definition): FormModel
    {
        try {
            $definitionParts = Arrays::extractKeys($definition, [
                'tag',
                'namespace',
                'action',
                'method',
                'sections',
            ], true);
        } catch (\Exception $exc) {
            throw new \InvalidArgumentException(
                "Definition must contain the keys 'tag','namespace','action','method','sections'"
            );
        }
        if (array_key_exists('wrapStack', $definition)) {
            $definitionParts['wrapStack'] = $definition['wrapStack'];
        }
        if ($definitionParts['tag'] !== 'form') {
            throw new \InvalidArgumentException("Form definition only supports the tag 'form'");
        }
        $class = get_called_class();
        $formModel = new $class(
            $definitionParts['namespace'],
            $definitionParts['method'],
            $definitionParts['action']
        );
        if (isset($definition['csrf'])) {
            $formModel->setCSRFStrategy($definition['csrf']);
        }
        foreach ($definitionParts['sections'] as $section) {
            if (isset($section['class'])) {
                $class = $section['class'];
                $name = $section['name'];
                $properties = $section['properties'] ?? [];
                $attributes = $section['attributes'] ?? [];
                $container = $section['container'] ?? null;
                $wrapStack = $section['wrapStack'] ?? null;
                unset($section['class'], $section['name']);
                $component = ComponentFactory::build($class, $formModel->namespace, $name, $attributes);
                $component->setAttributes($attributes);
                $component->setRegisteredPropertyValues($properties);
                if ($container) {
                    $component->setContainerAttributes($container);
                }
                if ($wrapStack) {
                    $component->setWrapStack($wrapStack);
                }
                $component->hydrate();
                $formModel->sections[$name] = $component;
            } elseif (isset($section['html'])) {
                $formModel->addHtml($section['html']);
            }
        }
        return $formModel;
    }

    /**
     * @param string $csrfStrategy
     * @throws \Exception
     */
    public function setCSRFStrategy(string $csrfStrategy): void
    {
        switch ($csrfStrategy) {
            case self::CSRF_STRATEGY_OFF:
            case self::CSRF_STRATEGY_COOKIE:
            case self::CSRF_STRATEGY_SESSION:
                $this->csrfStrategy = $csrfStrategy;
                break;
            default:
                throw new \Exception("Unrecognised CSRF strategy '" . $csrfStrategy . "'");
        }
    }

    /**
     * @throws \Exception
     */
    private function implementCSRFStrategy(): ?\Deform\Component\Input
    {
        switch ($this->csrfStrategy) {
            case self::CSRF_STRATEGY_SESSION:
                $this->ensureSessionStarted();
                $token = bin2hex(random_bytes(35));
                $_SESSION[$this->getCSRFFormTokenName()] = $token;
                return ComponentFactory::Hidden($this->namespace, self::CSRF_TOKEN_FIELD)->value($token);

            case self::CSRF_STRATEGY_COOKIE:
                $token = bin2hex(random_bytes(35));
                setcookie(
                    $this->getCSRFFormTokenName(),
                    $token,
                    [
                        'expires' => 0,// end of session
                        'path' => (strlen($this->formAction) > 0)
                            ? $this->formAction
                            : $_SERVER['REQUEST_URI'],
                        'domain' => $_SERVER['SERVER_NAME'],
                        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'),
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
                return ComponentFactory::Hidden($this->namespace, self::CSRF_TOKEN_FIELD)->value($token);

            case self::CSRF_STRATEGY_OFF:
                return null;

            default:
                throw new \Exception("Unrecognised CSRF strategy '" . $this->csrfStrategy . "'");
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function ensureSessionStarted(): void
    {
        $sessionStatus = session_status();
        if ($sessionStatus === PHP_SESSION_DISABLED) {
            throw new \Exception(
                "PHP sessions are disabled!"
            );
        } elseif ($sessionStatus === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return string
     */
    protected function getCSRFFormTokenName(): string
    {
        return $this->namespace . '-' . self::CSRF_TOKEN_FIELD;
    }

    /**
     * @param array $formData
     * @return bool
     * @throws \Exception
     */
    private function validateCSRFToken(array &$formData): bool
    {
        switch ($this->csrfStrategy) {
            case self::CSRF_STRATEGY_SESSION:
                $this->ensureSessionStarted();
                $tokenName = $this->getCSRFFormTokenName();
                if (!isset($formData[self::CSRF_TOKEN_FIELD]) || !isset($_SESSION[$tokenName])) {
                    return false;
                }
                $sessionToken = $_SESSION[$tokenName];
                $formToken = $formData[self::CSRF_TOKEN_FIELD];
                unset($_SESSION[$tokenName]);
                unset($formData[self::CSRF_TOKEN_FIELD]);
                return hash_equals($sessionToken, $formToken);

            case self::CSRF_STRATEGY_COOKIE:
                $tokenName = $this->getCSRFFormTokenName();
                if (!isset($formData[self::CSRF_TOKEN_FIELD]) || !isset($_COOKIE[$tokenName])) {
                    return false;
                }
                $cookieToken = $_COOKIE[$tokenName];
                $formToken = $formData[self::CSRF_TOKEN_FIELD];
                unset($formData[self::CSRF_TOKEN_FIELD]);
                unset($_COOKIE[$tokenName]);
                setcookie($tokenName, '', 1);
                return hash_equals($cookieToken, $formToken);

            case self::CSRF_STRATEGY_OFF:
                return true;

            default:
                throw new \Exception("Unrecognised CSRF strategy '" . $this->csrfStrategy . "'");
        }
    }

    /**
     * override to do something more useful!
     */
    protected function handleCSRFTokenFailure(): never
    {
        ob_end_clean();
        http_response_code(403);
        die("Forbidden");
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasComponent(string $field): bool
    {
        return isset($this->fieldComponents[$field]);
    }
}
