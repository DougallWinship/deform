# Codeception Tests
You can mount /_data/public/ in a local webserver to see exactly what is being tested by the acceptance tests.

Run the unit & acceptance tests like this:
```
./codecept run
```

Additionally generate a coverage report (to /_output/coverage) like this:
```
./codecept run --coverage-html
```