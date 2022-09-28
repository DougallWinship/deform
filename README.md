# Deform
Easily define consistent forms in PHP, which can be subsequently manipulated before rendering. 

## Why?
Form coding is highly repetitive & IDE auto-completion is your friend.

## Features
* consistent generation of components (as regards the HTML structure)
* strong IDE auto-completion support & chaining wherever appropriate
* generate forms in a controller action which can then be tailored in a view
* export a form to an array & build a form from an array definition (so you can persist them)
* todo: - auto-generate forms from a model or a database table
* todo: - sensible default form handling
* todo: - validation support (this maybe largely out of scope but it should at least be eased)
* todo: - rendering adapters (see next)
* todo: - shadow dom component generation

## Layers
There are 3 principal layers:
1. Deform\Html - generate an HTML tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

## Examples

Here are some very simple examples of each layer: 

> **_NOTE:_** If you set up /tests/_data/public/ as a doc root on a local webserver you can view what the 
> codeception acceptance tests see!    

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

... or via a (very) simple selector system:
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

### Deform\Form
Under construction!

## Dependencies
If you want to use CSS selectors (rather than XPath) you should install https://github.com/bkdotcom/CssXpath.

That's it!

## Tests

Run the codecept tests like this:
```
./codecept run
```

And with coverage html report generated to [tests/_output/coverage/index.html](tests/_output/coverage/index.html)
```
./codecept run --coverage-html
```

## Code style - PSR-12
The code is meant to conform to the PSR-12 standard as far as is sensible. 

This is the tool that is used to check : https://github.com/squizlabs/PHP_CodeSniffer

Once installed and available globally (presumably via PATH settings), something like this can be used from the root dir.
```
phpcs --standard=PSR12 ./src/Deform/
```

