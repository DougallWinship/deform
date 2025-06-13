[&laquo; back](../README.md)

# Examples

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
...will output:
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

If you want to do more complex manipulation you should load the tag into [HtmlDocument](../src/Deform/Html/HtmlDocument.php) (a basic DomDocument wrapper):
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

You can also generate an [HtmlTag](../src/Deform/Html/HtmlTag.php) from any arbitrary HTML string, rather than by 
chaining if you want to (for some, as yet undetermined, reason!):
```php
$htmlString = <<<HTML
<div style='border:10px solid red' class='outerdiv'>
    <hr style='border:10px solid green' class='innerhr'>
</div>
HTML;
$htmlTag = \Deform\Html\HtmlDocument::load($htmlString)->getHtmlRootTag();
```

> **_NOTE:_** There is no checking of the generated HTML for correctness. It's up to you to get it right!
> Also libxml behaviour can [change significantly](https://github.com/php/doc-en/issues/2219) & with little warning!
> ```\Deform\Html\HtmlDocument::load()``` should be considered a parser and *not* used for validation.

### Deform\Component
Components are built using Deform\Html. Where appropriate they are provided with a wrapper, and optionally a label, hint
and error.
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
Which looks like this:
```html
<div id="form1-myradiobuttonset-container" class="component-container container-type-radio-button-set" style="background-color:#333;display:inline-block;padding:8px;border-radius:8px">
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

> **_NOTE:_** Since components are defined as [HtmlTag](../src/Deform/Html/HtmlTag.php) instances you can perform any 
> manipulations on them as outlined in the previous section, they are only rendered as actual HTML when finally cast to
> a string.

#### Custom element definitions
The components can also generate a set of [Custom Elements](https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements) javascript definition
which can then be used natively or via any javascript framework that supports custom components.
```php
<script>
<?php echo \Deform\Component\ComponentFactory::getCustomElementDefinitionsJavascript() ?>
</script>
```
Please look at the tests [component page](../tests/_data/public/component/components.php) for examples.
> **_NOTE:_**
> concrete examples will be provided once this feature is more stable.

### Deform\Form
There is a [FormModel](../src/Deform/Form/FormModel.php) that can be used to build form definitions.
There is an example [here](../tests/_data/App/ExampleFormModel.php) which is used in the acceptance tests.

#### Features
* CSRF protection : disabled, session based, or double-submit cookie based.
* optional namespaces.
* unchecked values are explicit for Checkbox, CheckboxMulti, and RadioButtonSet when populateFormData(...) is called.
* there are validateFormData(...) and processFormData(...) methods which can/should be overloaded for form processing.
* there is a single run() method which can be used to perform form generation & processing in one go.
* generate an array definition from a form, and generate a form from such a definition (useful for db serialisation, or
  ajax submission etc.)

#### Out of scope
* DB model systems can easily be used but are not directly supported.
* Validation is the responsibility of the user, however there are
 - a validateFormData(...) method in the FormModel class which can be overloaded.
 - a setErrors(...) method form manually specifying any form errors, which matches fields to their Component and
   displays any issues.
