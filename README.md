# Deform
Easily define consistent forms in PHP, which can be subsequently manipulated before rendering.

> Beware : although this library has had quite a bit of work put into it, currently
> it's only been used for a single public facing project & hence should not be considered battle hardened!

## Why?
Form coding is highly repetitive & IDE auto-completion is your friend.

## Installation
Usage requires a PSR-4 compatible autoloader.

### With composer
As there is not yet a stable release install like this
```
composer require dougallwinship/deform:dev-master
```

### Manual
Move to a suitable dir such as '/libs' then
```
git clone https://github.com/DougallWinship/deform.git
```

Make the /deform/src dir available to autoload.

> You may not be using composer, but you will need to use a PSR-4 autoloader to ```/src``` to load the library with
> the root namespace ```\Deform```
>
> If you were to manually do so for now you'll have to figure it out on your own, but the [composer.json](./composer.json)
> definition is something like:
> ```
>    "autoload": {
>        "psr-4": {"Deform": "libs/deform/src/Deform"}
>    },
>```

___

# Features
* consistent generation of components (as regards the HTML structure)
* strong IDE auto-completion support & chaining wherever appropriate
* generate forms in a controller action which can subsequently be tailored in a view
* export a form to an array & build a form from an array definition (so you can persist them via json etc.)
* custom HTML element creation using shadow DOM

## Layers
There are 3 principal layers:
1. Deform\Html - generate an HTML tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

### ToDo:
* change acceptance tests to use a real browser (via selenium) & test custom components (etc)
* add instructions/examples on styling
* add instructions/examples on making your own components
* add instructions/examples on the form layer

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
available. The authority is the annotations listed in [ComponentFactory](src/Deform/Component/ComponentFactory.php).

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
it using selectors. Please see [build.php](tests/_data/public/form/build.php) 
and [document.php](tests/_data/public/html/document.php) for example usage.

You can also convert the form to an array definition, or build a form from an array definition.
```php
$loginFormDefinition = $loginForm->toArray();
$rebuiltLoginForm = FormModel::buildForm($loginFormDefinition);
```

## Examples

Here are some simple examples of each layer: 

> **_NOTE:_** If you set up /tests/_data/public/ as a doc root on a local webserver you can view what the 
> codeception acceptance tests see (you'll want to rewrite all urls to the index.php).
> 
> Using PHP's built in webserver, something like this:   
> ```php -S localhost:8000 ./tests/_data/public/router.php```

### Deform\Html
Tools for generating HTML programmatically.
```php
use \Deform\Html\Html as Html;

$html = Html::div()->css('border','10px solid red'])->class('outerdiv')->add(
    Html::hr()->css('border','10px solid green'])->class('innerhr')
);
echo $html;
```
...will output (indent & newlines added for readability):
```html
<div style='border:10px solid red' class='outerdiv'>
    <hr style='border:10px solid green' class='innerhr'>
</div>
```

... and can then be manipulated:
```php
echo $html->css('color','blue')->clear()->add('Blue Text');
```
... will output:
```html
<div style="border:10px solid red;color:blue" class="outerdiv">Blue Text</div>
```

... or via a (*very*) simple selector system:
```php
echo $html->deform('.blue-text',function(\Deform\Html\HtmlTag $node) {
    $node->css('color','green')->reset('Green Text'); /* reset() is the same as clear() and then add() */
});
```

If you want to do more complex manipulation you should load the tag into an HtmlDocument (a basic DomDocument wrapper):
```php
$document = \Deform\Html\HtmlDocument::loadHtmlTag($html)
    ->selectXPath('.//*[contains(concat(" ",normalize-space(@class)," ")," blue-text ")]', function(\DOMElement $domElement) {
        $domElement->nodeValue='Changing the text again';
        $domElement->setAttribute('style','red')    
    })
```

> **_NOTE:_** Ugh! XPath's selectors can be ugly! 
> You can alternatively use selectCss(...) directly if you install https://github.com/bkdotcom/CssXpath via composer,
> otherwise an online conversion tool such as https://css2xpath.github.io/ can be useful.

You can also generate an HtmlTag from an arbitrary HTML string rather than by chaining if you want to for some, as yet 
undetermined, reason:
```php
$htmlString = <<<HTML
<div style='border:10px solid red' class='outerdiv'>
    <hr style='border:10px solid green' class='innerhr'>
</div>
HTML;
$htmlTag = \Deform\Html\HtmlDocument::load($htmlString)->getHtmlRootTag();
```

> **_NOTE:_** There is no checking of the generated HTML for correctness. It's up to you to get it right!

### Deform\Component
Components are built using Deform\Html. Where appropriate they are provided with a wrapper and a label by default.
```php
use \Deform\Component\ComponentFactory as Component;

echo Component::RadioButtonSet('form1', 'myradiobuttonset')
        ->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])
        ->label("My Radio Button Set");
```
...will output something like this:
```html
<div id="form1-myradiobuttonset-container" class="component-container container-type-radio-button-set">
    <div class="label-container">
        <label>My Radio Button Set</label>
    </div>
    <div class="control-container">
        <div class="radio-button-container">
            <input type="radio" value="one" id="radiobuttonset-form1-myradiobuttonset-One" name="form1[myradiobuttonset]">
            <label for="radiobuttonset-form1-myradiobuttonset-One">One</label>
        </div>
        <div class="radio-button-container">
            <input type="radio" value="two" id="radiobuttonset-form1-myradiobuttonset-Two" name="form1[myradiobuttonset]">
            <label for="radiobuttonset-form1-myradiobuttonset-Two">Two</label>
        </div>
        <div class="radio-button-container">
            <input type="radio" value="three" id="radiobuttonset-form1-myradiobuttonset-Three" name="form1[myradiobuttonset]">
            <label for="radiobuttonset-form1-myradiobuttonset-Three">Three</label>
        </div>
        <input type="hidden" name="form1[expected_data][]" value="myradiobuttonset">
    </div>
</div>
```

> **_NOTE:_** Since components are defined as HtmlTag instances you can perform any manipulations on them outlined in 
> the previous section, they are only rendered as actual HTML when cast to a string.

#### Custom element definitions
The components can also generate a set of [Custom Elements](https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements)
via javascript which can then in turn be used in javascript frameworks which support custom elements (such as Vue/React).
```php
<script>
<?php echo \Deform\Component\ComponentFactory::getCustomElementDefinitionsJavascript() ?>
</script>
```
Which can then be used like this:
```html
<form name="potatoes" data-namespace="potatoes">
    <deform-button name='button1' value='buttonvalue' label='Button Label' onclick="this.parentNode.submit()">Button</deform-button><br>
    <deform-checkbox name='checkbox1' value="checkboxvalue" label="Checkbox Label"></deform-checkbox><br>
    <deform-checkbox-multi name='checkbox-multi1' values='{"one":"One","two":"Two","three":"Three"}' label='CheckboxMulti Label'></deform-checkbox-multi><br>
    <deform-currency name='currency1' currency="&pound;" label='Currency Label'></deform-currency><br>
    <deform-date name='date1' label='Date Label'></deform-date><br>
    <deform-date-time name='datetime1' label='DateTime Label'></deform-date-time><br>
    <deform-display name='display1' label='Display Label' value='show this'></deform-display><br>
    <deform-email name='email1' label='Component Email' value='potatoes'></deform-email><br>
    <deform-file name='file1' label='Component File'></deform-file><br>
    <deform-image name='image1' label='Component Image'></deform-image><br>
    <deform-multiple-file name='multiplefile1' label='Component Multiple File'></deform-multiple-file><br>
    <deform-multiple-email name='multipleemail1' label='Component Multiple Email'>Button</deform-multiple-email><br>
    <deform-hidden name='hidden1' value='hiddenvalue'></deform-hidden> &laquo;Hidden Input<br><br>
    <deform-input-button name='inputbutton1' label='Component Input Button' value='value1' label='Input Button Label'></deform-input-button><br>
    <deform-password name='password1' label='Component Password' value='password1' label='Password Label'></deform-password><br>
    <deform-radio-button-set name='radiobuttonset1' label='Component Radio Button Set' values='{"one":"One","two":"Two","three":"Three"}' label='Radio Buton Set Label'></deform-radio-button-set><br>
    <deform-select name='select1' label="component-select" options='{"one":"One","two":"Two","three":"Three"}' label='Select Label'></deform-select>
    <deform-select-multi name='selectmulti1' options='{"one":"One","two":"Two","three":"Three"}' label='Select Multi'></deform-select-multi>
    <deform-slider name='slider1' label='Slider Label' min="50" max="150" showOutput="true"></deform-slider><br>
    <deform-submit name='submit1' value="potatoes"></deform-submit><br>
    <deform-text name='text1' label='Text Label' value='text value'></deform-text><br>
    <deform-text-area name='textarea1' label='component-text-area'>this is some text area value</deform-text-area><br>
</form>
```

### Deform\Form
There is a [FormModel](src/Deform/Form/FormModel.php) that can be used to build form definitions, 
[here](tests/_data/App/ExampleFormModel.php) is an example used in the tests.

TODO: - make this section useful!

## Dependencies
As previously noted, if you want to use CSS selectors (rather than XPath) you should install https://github.com/bkdotcom/CssXpath.

That's it!

## Tests

See [tests/README.md](tests/README.md)

## Code style - PSR-12
The code is meant to conform to the PSR-12 standard as far as is sensible. 

This is the tool that is used to check : https://github.com/PHPCSStandards/PHP_CodeSniffer/

Once installed and available globally (presumably via PATH settings), something like this can be used from the root dir.
```
phpcs --standard=PSR12 ./src/Deform/
```

