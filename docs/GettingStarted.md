[&laquo; back](../README.md)

## Getting started

### Raw components
Components can be used directly in a view file like this
```php
<?php use \Deform\Component\ComponentFactory as Component; ?>
<?= Component::Text('namespace','text-field')
    ->label('Text Field Label')
    ->value('initial value')
    ->hint('text field hint');
?>
```
Your IDE should help with auto-completion lists to see what components are currently
available. The authority is the annotations listed in [ComponentFactory](../src/Deform/Component/ComponentFactory.php).

### Form Model
While it's possible to use the components manually, it's recommended to make a FormModel to represent a set of
components & specify what you wish to do with them.
```php
<?php

namespace App/Form;

class LoginForm extends \Deform\Form\FormModel
{
    public function __construct() 
    {
        parent::__construct('login-form');
        $this->addHtml("<h1>Login</h1>");
        $this->addText('email')->label('Email');
        $this->addPassword('password')->label('Password');
        $this->addDisplay('login-failed-message');
        $this->addSubmit('Login');
    }
    
    public function validateFormData(array $formData) {
        if (!isset($formData['email']) || !isset($formData['password'])) {
            throw new \Exception('Unexpected missing form data');
        }
        $errors = [];
        if (!$formData['email']) {
            $errors['email']='Missing email';
        }
        elseif (!filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
            $errors['email']='Invalid email';
        }
        if (!$formData['password']) {
            $errors['password']='Missing password';
        }
        return count($errors)===0 
            ? true 
            : $errors;
    }
    
    public function processFormData(array $formData) 
    {
        // obviously this assumes you have an Auth class!
        if (Auth::checkCredentials()) {
            Auth::login($formData['email'], $formData['password']);
            Auth::redirectAfterLogin()
        }
        else {
            $this->getFieldComponent('login-error-message')->value("Email or password was incorrect");
        }
    }
}
```

Typically, you would instantiate this in a controller action as follows:
```php
$loginForm = new LoginForm();// $loginForm is now an HtmlTag containing the form
$loginForm->run();
```
Then pass $loginForm to your view:
```php
<?= $loginForm ?>
```

The reason this library is called deform is that you can manipulate the form in the view (or action) prior to displaying
it using selectors. Please see [build.php](../tests/_data/public/form/build.php)
and [document.php](../tests/_data/public/html/document.php) for example usage.

You can also convert the form to an array definition, or build a form from an array definition.
```php
$loginFormDefinition = $loginForm->toArray();
$rebuiltLoginForm = FormModel::buildForm($loginFormDefinition);
```
