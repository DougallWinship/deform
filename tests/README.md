# Codeception Tests
You can mount /_data/public/ in a local webserver to see exactly what is being tested by the acceptance tests.

Using Laravel Valet this can be accomplished by navigating to that directory then
```
valet link deform-acceptance
```
... after which you can visit http://deform-acceptance.test

## Db
Unless you want to manually alter the codeception settings, you should create a local db called 'deform-test-db' which 
can be accessed via host='local' user='root' and password='root'.

## Troubleshooting

 * ``` XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set```
