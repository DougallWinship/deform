[&laquo; back](../README.md)

## Layer Examples

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

> **_NOTE:_** Since components are defined as HtmlTag instances you can perform any manipulations on them outlined in
> the previous section, they are only rendered as actual HTML when finally cast to a string.

#### Custom element definitions
The components can also generate a set of [Custom Elements](https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements)
via javascript which can then in turn be used directly in a page or via any javascript framework that support custom
elements (such as Vue/React).
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
    <deform-radio-button-set name='radiobuttonset1' label='Component Radio Button Set' values='{"one":"One","two":"Two","three":"Three"}' label='Radio Button Set Label'></deform-radio-button-set><br>
    <deform-select name='select1' label="component-select" options='{"one":"One","two":"Two","three":"Three"}' label='Select Label'></deform-select>
    <deform-select-multi name='selectmulti1' options='{"one":"One","two":"Two","three":"Three"}' label='Select Multi'></deform-select-multi>
    <deform-slider name='slider1' label='Slider Label' min="50" max="150" showOutput="true"></deform-slider><br>
    <deform-submit name='submit1' value="potatoes"></deform-submit><br>
    <deform-text name='text1' label='Text Label' value='text value'></deform-text><br>
    <deform-text-area name='textarea1' label='component-text-area'>this is some text area value</deform-text-area><br>
</form>
```

### Deform\Form
There is a [FormModel](../src/Deform/Form/FormModel.php) that can be used to build form definitions.
There is an exmaple [here](../tests/_data/App/ExampleFormModel.php) which is used in the acceptance tests.

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
* Validation is the responsibility of the user, however there is a convenient setErrors(...) method to which any
  validation errors can be sent, this matches fields to their Component and displays any issues.

TODO: - make this section more useful with examples etc.
