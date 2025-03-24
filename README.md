# Deform
Deform helps you build consistent HTML forms with PHP.

Components are rendered as standard HTML/CSS by default, but can also be exported as JavaScript [custom elements](https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements) for use in any frontend framework.

## Content
[Features](./docs/Features.md)  
[Installation](#installation)
[Getting Started](./docs/GettingStarted.md)  
[Examples](./docs/Examples.md)  
[Project Info](./docs/ProjectInfo.md)  

You can contact me here : [dougall.winship@gmail.com](mailto:dougall.winship@gmail.com)

## Quick Demo

### Direct Component Usage:
![output](./docs/example.gif)
which generates, with whitespace added for readability, the following:
```html
<form action="" method="post">
    <div id='login-email-container' class='component-container container-type-text'>
        <div class='label-container'><label style='margin-bottom:0' for='text-login-email'>Email <span class="required">*</span></label></div>
        <div class='control-container'><input id='text-login-email' name='login[email]' type='text'></div>
    </div>
    <div id='login-password-container' class='component-container container-type-password'>
        <div class='label-container'><label style='margin-bottom:0' for='password-login-password'>Password <span class="required">*</span></label></div>
        <div class='control-container'><input id='password-login-password' name='login[password]' type='password'></div>
    </div>
    <div class="center"><input id='submit-login-Login' name='login[Login]' type='submit' value='Login'></div>
</form>
```

## Installation
<a name='installation'></a>

Usage requires a PSR-4 compatible autoloader.

### With composer
As there is not yet a stable release install like this
```
composer require dougallwinship/deform:dev-master
```

### Manual
Move to a suitable dir such as '/libs' then
```
git clone https://github.com/DougallWinship/deform.git
```

Make the /deform/src dir available to autoload.

> You may not be using composer, but you will need to use a PSR-4 autoloader to ```/src``` to load the library with
> the root namespace ```\Deform```
>
> If you were to manually do so for now you'll have to figure it out on your own, but the [composer.json](./composer.json)
> definition is something like:
> ```
>    "autoload": {
>        "psr-4": {"Deform": "libs/deform/src/Deform"}
>    },
>```

___

### ToDo:
* add/update example gifs on this page
* change acceptance tests to use a real browser (via selenium) & test custom components (etc.)
* add instructions/examples on styling
* add instructions/examples on making your own components
* improve instructions/examples on the form layer
