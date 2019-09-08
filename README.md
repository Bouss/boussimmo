# Immo' Scrap

Find the property adds that fit your needs from a lot of sources, real estate websites as real estate agents.

Usage
-----

Compare all the property ads extracted from your mails on one page.

## Sites

- Bien'ici
- FNAIM
- Logic-Immo
- OuestFrance-Immo
- SeLoger


Requirements
------------

- PHP 7.3.9 or higher
- Node.js 10 or higher
- Composer
- Yarn

Installation
-------------

```
$ composer install
$ yarn install
```

Configuration
-------------

Fill the env variables `WEBMAIL_LOGIN`, `WEBMAIL_PASSWORD` and `WEBMAIL_FOLDER` in your `.env.local` file with your Gmail credentials.   
You may enable "no-certificate applications" in your Gmail account configuration in order to access your mails from this project.

Tests
-----

```
$ ./bin/phpunit
```

Technologies
------------

- Symfony 4.3
- Webpack Encore
- CSS3 with BEM methodology
