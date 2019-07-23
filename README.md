# Immo' Scrap

Find the property adds that fit your needs from a lot of sources, real estate websites as real estate agents.

Requirements
------------

- PHP 7.2 or higher
- Composer
- MySQL database

Configuration
-------------

- `.env` : set `DATABASE_URL` and `DATABASE_URL_TEST` environnement variables : replace `db_user`, `db_password`, `db_name` and `test_db_name` with your own values
- `phpunit.xml` : set `DATABASE_URL_TEST` environnement variable : replace `db_user`, `db_password` and `test_db_name` with your own values

Installation
-------------

```
$ make install
```

Tests
------

```
$ make test
```

