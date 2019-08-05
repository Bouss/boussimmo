# Immo' Scrap

Find the property adds that fit your needs from a lot of sources, real estate websites as real estate agents.

Usage
-----

```
bin/console app:scrap <site> <city> <property-type> -p<max-price> -a<min-area> -r<min-rooms-count>
```

## Sites

- OuestFrance-Immo (`ouestfrance-immo`)
- SeLoger (`seloger`)
- Logic-Immo (actually not working because of bot detection) (`logic-immo`)

## Cities

- Nantes (`nantes`)
- Rennes (`rennes`)

## Property types

- Apartment (`1`)
- House (`2`)

Requirements
------------

- PHP 7.2 or higher
- Composer
- Chromium
- ext-zip PHP extension

Installation
-------------

```
$ composer install
```

Tests
-----

```
$ ./bin/phpunit
```

Technologies
------------

- Symfony 4.3
- Symfony Panther
