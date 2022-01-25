#Components
Hybrid components to facilitate form building in a consistent fashion as regards labels, error messages, etc.
For example in this context an &lt;input&gt; may well want to be wrapped like this
```
<div class='component-wrapper'>
  <div class='label-wrapper'>
    <label for='my-input'>My Input</label>
  </div>
  <div class='control-wrapper'>
    <input type='text' id='my-input' name='my-input' value='' />
  </div>
  <div class='hint-subtext'>something about this field</div>
</div>
```

Another goal is to allow manipulation of the form *before* the final HTML string is generated; for MVC (or equivalent) 
typically a form will be built and processed in a controller (using a model), and then rendered in a view. 
It's useful that:
- a form building system can use these components as building blocks to ensure consistency
- last minute tweaks to the form can occur in a view since this is where presentation occurs

## Notes:

* Components represent a form field (i.e. something that generates form data)

* Components *may* have a Component Container ... this will contain supporting tags (& structure) such as labels, error
  fields etc
  
* couple of definitions
  Component root = either the Container if there is one, or else the Component itself
  IHtml tree = a tree structure containing IHtml elements and children, strings are also permitted as leaves

* Each component must contain *at* *least* *one* Component Control, this is any tag which generates form data or 
  otherwise interacts with it (e.g. form submission)

* Some examples
    - the Submit component has no container, and a single control represents the &lt;input type='submit'&gt; tag
    - the CheckBoxMulti component has a container, and multiple controls to represent the various &lt;input type='checkbox'&gt; tags 
    - the Input component has a container, and a single control representing the &lt;input type='text'&gt;
     
* currently, components are responsible for:
   - rendering themselves including their optional container, all controls, and allsupporting tags such as error 
     placeholder and label(s) 
   - prior to rending providing abstract access to the html they will generate
   - providing suitable mechanisms for conveniently setting control values, specifying errors, etc
  
* component rendering has an intermediate stage, see BaseComponent:convertToHtml(), which ensure the component is
  prepared as an IHtml tree

* Components are built via the ComponentFactory to allow chaining and auto-completion, for example
    ```  
      <?php use  \Deform\Component\ComponentFactory as Component; ?>  
      <?= Component::select('formname','fieldname'); ?>
    ```
     
* In order to prevent clashes between form related elements with names (such as inputs) a namespacing system is used.
  For example an input with the name 'email-address' and the namespace 'contact-form' will generate a name for the
  input component like this : email-address[contact-form]
  This will then arrive in the GET/POST data for PHP in this form:
  ```
    [
        'contact-form' => [
            'email-address' => 'example@address' 
        ]
    ]
  ```
  
* Components attempt to follow a standard order (via the ComponentContainer) where appropriate in order to facilitate
  consistent styling

