# Codeception Tests
You can mount /_data/public/ in a local webserver to see exactly what is being tested by the acceptance tests.

## Db
Unless you want to manually alter the codeception settings, you should create a local db called 'deform-test-db' which 
can be accessed via host='local' user='root' and password='root'.

If you want to suppress the command line warnings like this 
```
"Warning: Using a password on the command line interface can be insecure."
```
Then try this
```
mysql_config_editor set --login-path=local --host=localhost --user=root --password
```