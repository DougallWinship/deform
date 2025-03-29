[&laquo; back](../README.md)

# Project Info

### Dependencies
As previously noted, if you want to use CSS selectors (rather than XPath) you should install https://github.com/bkdotcom/CssXpath.

That's it!

### Tests
See [tests/README.md](../tests/README.md)

### Code style - PSR-12
The code is meant to conform to the PSR-12 standard as far as is sensible.

This is the tool that is used to check : https://github.com/PHPCSStandards/PHP_CodeSniffer/

Once installed and available globally (presumably via PATH settings), something like this can be used from the root dir.
```
phpcs --standard=PSR12 ./src/Deform/
```
### PHPDocumentor
If you have [PHPDocumentor](https://docs.phpdoc.org/) installed then you can generate API documentation like this
(assuming it's on your PATH)
```bash
phpdoc run -d src -t docs/api
```
This will create the API docs [/docs/api](./api/index.html).

To view the docs either manually open in a browser or mount on a local webserver.