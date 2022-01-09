# Deform
Easily define consistent forms which can be subsequently manipulated (or deformed!). 

## Why?
Forms are repetitive to code.

## Features
* generate forms in a controller action which can then be tailored in a view
* consistent generation of components
* IDE auto-completion support
* todo: - auto-generate forms from a model or a database table
* todo: - sensible default form handling

## Layers
There are 3 layers:
1. Deform\Html - generate an html tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

## Examples

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

The html can be manipulated like this:
```php
echo $html->clear()->add('Blue Text')->css('color','blue');
```
...which will output:
```html
<div style="border:10px solid red;color:blue" class="outerdiv" onclick="alert('div')">Blue Text</div>
```

Or via a (very) simpler selector:
```php
echo $html->deform('.blue-text',function(\Deform\Html\HtmlTag $node) {
    $node->clear()->css('color','green')->clear()->add('Green Text');
});
```

If you want to do more complex manipulation you should load the tag into an HtmlDocument (a basic DomDocument wrapper):
```php
$document = \Deform\Html\HtmlDocument::loadHtmlTag($html)
    ->selectCss(".blue-text", function(\DOMElement $domElement) {
        $domElement->nodeValue='Changing the text again';
        $domElement->setAttribute('style','red')    
    })
```

You can also generate an HtmlTag from an arbitrary HTML string rather than by chaining if you want:
```php
$htmlString = <<<HTML
<div style='border:10px solid red' class='outerdiv'>
    <hr style='border:10px solid green' class='innerhr'>
</div>
HTML;
$htmlTag = \Deform\Html\HtmlDocument::loadHtmlString($htmlString)->getHtmlRootTag();
```

### Deform\Component
Components are built using Deform\Html. Where appropriate they are provided with a wrapper and a label by default.
```php
use \Deform\Component\ComponentFactory as Component;

echo Component::RadioButtonSet('form1', 'myradiobuttonset')
        ->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])
        ->label("My Radio Button Set");
```
...will output:
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

### Deform\Form
Under construction!

## Dependencies
If you want to use CSS selectors (rather than XPath) you should install https://github.com/bkdotcom/CssXpath.

That's it!
