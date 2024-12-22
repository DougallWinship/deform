# Codeception Tests
You can mount /_data/public/ in a local webserver to see exactly what is being tested by the acceptance tests.

For example, using PHP's built-in webserver:
```
cd tests/_data/public
php -S localhost:8000
```
...then visit http://localhost:8000 in your browser.

Run the unit & acceptance tests like this:
```
./codecept run
```

Additionally generate a coverage report (to /_output/coverage) like this:
```
./codecept run --coverage --coverage-html
```

During coverage report generation you may see this error:
```XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set```
If so then you can either run it like this
```XDEBUG_MODE=coverage && ./codecept run --coverage --coverage-html```
or update your php.ini settings for xdebug with multiple available modes like this
```xdebug.mode=develop,debug,coverage```
... and then restart php-fpm