[&laquo; back](../README.md)

# Features
* form component generation (with consistent structure)
* full form model definition
* form submission handling with CSRF protection
* strong IDE auto-completion support & chaining wherever appropriate
* just-in-time DOM modification : via  (very) simplistic PHP selectors, full XPath selector, or optionally CSS selectors (*)
* form array (and hence json) encoding/decoding

> (*) - this requires a CSS to XPath conversion library such as https://github.com/bkdotcom/CssXpath

> **_NOTE:_** validation implementations are outside the scope of the project, but other libraries such as
> https://github.com/rakit/validation can easily be integrated.

### Layers
There are 3 principal layers:
1. Deform\Html - generate an HTML tree which can be manipulated
2. Deform\Component - generate various components using Deform\Html
3. Deform\Form - generate forms using Deform\Component

