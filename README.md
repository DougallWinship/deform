
# Deform

![PHP](https://img.shields.io/badge/PHP-%5E8.3-8892BF?logo=php&style=flat-square)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-green.svg?style=flat-square)
[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg?style=flat-square)](https://github.com/DougallWinship/deform/blob/master/LICENSE)
[![PSR-12](https://img.shields.io/github/actions/workflow/status/DougallWinship/deform/phpcs.yml?branch=master&label=PSR-12&style=flat-square)](https://github.com/DougallWinship/deform/actions/workflows/phpcs.yml)
[![Codeception](https://img.shields.io/github/actions/workflow/status/DougallWinship/deform/codeception.yml?branch=master&label=Codeception&style=flat-square)](https://github.com/DougallWinship/deform/actions/workflows/codeception.yml)

Deform helps you build and process consistent HTML forms with PHP.

Components are rendered as standard HTML/CSS by default, but can also be exported as JavaScript 
[custom elements](https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements).

![output](./docs/example.gif)

## Contents
[Installation](#installation)  
[Getting Started](#getting-started)  
[Features](./docs/Features.md)  
[Examples](./docs/Examples.md)  
[Styling](./docs/Styling.md)  
[Project Info](./docs/ProjectInfo.md)  
[Contact](#contact)

## Quick Demo

Direct component usage:
```php
<?php
use \Deform\Component\ComponentFactory as Component;
?>
<form action="" method="post">
    <?= Component::Text('login', 'email')->label('Email', true); ?>
    <?= Component::Password('login', 'password')->label('Password', true); ?>
</form>
```
generates:
```html
<form action="" method="post">
    <div id='login-email-container' class='component-container container-type-text'>
        <div class='label-container'><label style='margin-bottom:0' for='text-login-email'>Email <span class="required">*</span></label></div>
        <div class='control-container'><input id='text-login-email' name='login[email]' type='text'></div>
    </div>
    <div id='login-password-container' class='component-container container-type-password'>
        <div class='label-container'><label style='margin-bottom:0' for='password-login-password'>Password <span class="required">*</span></label></div>
        <div class='control-container'><input id='password-login-password' name='login[password]' type='password'></div>
    </div>
    <div class="center"><input id='submit-login-Login' name='login[Login]' type='submit' value='Login'></div>
</form>
```

## Installation
<a name='installation'></a>

With [composer](https://getcomposer.org/):

```
composer require dougallwinship/deform:dev-master
```

Alternatively you can manually install via git.
```
git clone https://github.com/DougallWinship/deform.git
```

Then move the code to a suitable directory and add a PSR-4 autoloader.

___

## Getting started
<a name='getting-started'></a>

#### Raw components
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
available. The authority is the annotations listed in [ComponentFactory](./src/Deform/Component/ComponentFactory.php).

#### Form Model
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
        if (Auth::checkCredentials($formData['email'], $formData['password'])) {
            Auth::login($formData['email'], $formData['password']);
            Auth::redirectAfterLogin()
        }
        else {
            $this->getFieldComponent('login-error-message')
                ->value("Email or password was incorrect");
        }
    }
}
```

Typically, you would instantiate this in a controller action as follows:
```php
$loginForm = new LoginForm();
$loginForm->run();
```
Then pass $loginForm to your view:
```php
<?= $loginForm->getFormHtml(); ?>
```

> The reason this library is called deform is that you can manipulate the form using selectors. 
> Please see [build.php](./tests/_data/public/form/build.php)
> and [document.php](./tests/_data/public/html/document.php) for example usage.

You can also convert the form to an array definition, and build a form instance from an array definition.
```php
$loginFormDefinition = $loginForm->toArray();
$rebuiltLoginForm = FormModel::buildForm($loginFormDefinition);
```
___

### Roadmap:
* change acceptance tests to use a real browser (via selenium) & test custom components (etc.)
* add instructions/examples on making your own components
* improve the usage instructions/examples (particularly for the form layer)

___

### Contact
<a name='contact'></a>

You can contact me here : [dougall.winship@gmail.com](mailto:dougall.winship@gmail.com)
