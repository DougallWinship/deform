# Deform
Easily define consistent forms in PHP, which can be subsequently manipulated before rendering. 

## Why?
Form coding is highly repetitive & IDE auto-completion is your friend.

## Features
* consistent generation of components (as regards the HTML structure)
* strong IDE auto-completion support & chaining wherever appropriate
* generate forms in a controller action which can then be tailored in a view
* export a form to an array & build a form from an array definition (so you can persist them via json etc.)
* custom HTML element creation using the shadow dom

### Planned
* auto-generate forms from a model or a database table
* sensible default form handling
* validation support (this maybe out of scope but it should at least be made as easy as possible)
* rendering adapters
* make styling easy 

## Layers
There are 3 principal layers:
1. Deform\Html - generate an HTML tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

## Examples

Here are some very simple examples of each layer: 

> **_NOTE:_** If you set up /tests/_data/public/ as a doc root on a local webserver you can view what the 
> codeception acceptance tests see (you'll want to rewrite all urls to the index.php).
> 
> Using php's built in webserver on port 8000:   
> ```php -S localhost:8000 ./tests/_data/public/router.php```

### Deform\Html

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

> **_NOTE:_** Ugh! XPath selectors can be ugly! You can alternatively use selectCss(...) if you install https://github.com/bkdotcom/CssXpath via composer.

You can also generate an HtmlTag from an arbitrary HTML string rather than by chaining if you want to for some, as yet undetermined, reason:
```php
$htmlString = <<<HTML
<div style='border:10px solid red' class='outerdiv'>
    <hr style='border:10px solid green' class='innerhr'>
</div>
HTML;
$htmlTag = \Deform\Html\HtmlDocument::loadHtmlString($htmlString)->getHtmlRootTag();
```

> **_NOTE:_** There is no checking of the generated HTML for correctness. It's up to you to get it correct!

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

> **_NOTE:_** You can see all the available components by looking at the annotations of the [ComponentFactory](src/Deform/Component/ComponentFactory.php).

#### Custom element definitions
The components can also generate a set of customElements via javascript.
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
Still under construction!

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

