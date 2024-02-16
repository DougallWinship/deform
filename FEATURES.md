# Features
* consistent generation of components (as regards the HTML structure)
* strong IDE auto-completion support & chaining wherever appropriate
* generate forms in a controller action which can then be tailored in a view
* export a form to an array & build a form from an array definition (so you can persist them via json etc.)
* custom HTML element creation using the shadow dom

## Layers
There are 3 principal layers:
1. Deform\Html - generate an HTML tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

### Planned
* auto-generate forms from a model or a database table
* sensible default form handling
* validation support (this maybe out of scope but it should at least be made as easy as possible)
* rendering adapters
* make styling easy