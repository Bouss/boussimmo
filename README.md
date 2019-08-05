# Immo' Scrap

Find the property adds that fit your needs from a lot of sources, real estate websites as real estate agents.

Usage
-----

```
bin/console site:scrap <site> <"nantes"|"rennes"> < 0 (House) | 1 (Apartment)> -p<max-price> -a<min-area> -r<min-rooms-count>
```

Working sites
-------------

- OuestFrance-Immo
- SeLoger

Requirements
------------

- PHP 7.2 or higher
- Composer
- Chromium
- ext-zip PHP extension

Tests
-----

```
$ ./bin/phpunit
```

Installation
-------------

```
$ composer install
```

Technologies
------------

- Symfony 4.3
- Symfony Panther
