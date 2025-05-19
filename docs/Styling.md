[&laquo; back](../README.md)

# Styling

There is a default [stylesheet](../assets/deform.css) used by the acceptance test code. This should help you 
get started with customisation.

It provides styling for both the HTML and CustomElement components (along with some basic form/html rules).

The custom elements are automatically decorated with 'part' attributes to allow styling via 
[::part](https://developer.mozilla.org/en-US/docs/Web/CSS/::part) selectors, the rule is as follows:
- if the shadow dom tag has any classes they are used for the part definition(s)
- otherwise if the tag has a 'type' attribute that is used along with the tag name
- otherwise just the tag name itself is used, 

For example the Slider custom element's shadow DOM is as follows: 
```html
<div id="deform-slider">
  <div class="component-container container-type-slider" part="deform-component-container deform-container-type-slider">
    <div class="label-container" part="deform-label-container">
      <label style="margin-bottom:0" for="slider-namespace-name" part="deform-label">Slider Label <span class="required" part="deform-required deform-hidden">*</span>
      </label>
    </div>
    <div class="control-container" part="deform-control-container">
      <input id="slider-namespace-name" name="namespace[slider1]" type="range" part="deform-input deform-input-range" min="50" max="150">
    </div>
    <div class="hint-container" part="deform-hint-container deform-hidden">{hint}</div>
    <div class="error-container" part="deform-error-container deform-hidden">{error}</div>
  </div>
</div>
```

Note that ```deform-hidden``` is a special case as it's used to show/hide shadow dom tags, if you want to write your own
css entirely you should, at a bare minimum, provide this:
```css
*::part(deform-hidden) {
    display: none !important;
}
```
