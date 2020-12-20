# CarNext Todo project

Create the database
```
bin/console doctrine:database:create
```

Create tables
```
bin/console doctrine:schema:update --force
```

Start the server
```
symfony server:start
```

Run the tests
```
bin/console doctrine:database:create -e test
bin/console doctrine:schema:update --force -e test
bin/phpunit
```
