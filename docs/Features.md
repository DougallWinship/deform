[&laquo; back](../README.md)
## Features
* consistent generation of form components (as regards the HTML structure)
* strong IDE auto-completion support & chaining wherever appropriate
* generate forms in a controller action which can subsequently be tailored in a view
* export a form definition to an array & build a form from an array definition
* custom HTML element creation using shadow DOM

### Layers
There are 3 principal layers:
1. Deform\Html - generate an HTML tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

### ToDo:
* change acceptance tests to use a real browser (via selenium) & test custom components (etc.)
* add instructions/examples on styling
* add instructions/examples on making your own components
* improve instructions/examples on the form layer
