
# Boussimmo

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Bouss/boussimmo/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Bouss/boussimmo/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/Bouss/boussimmo/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Bouss/boussimmo/?branch=develop) [![Build Status](https://scrutinizer-ci.com/g/Bouss/boussimmo/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Bouss/boussimmo/build-status/develop) [![Code Intelligence Status](https://scrutinizer-ci.com/g/Bouss/boussimmo/badges/code-intelligence.svg?b=develop)](https://scrutinizer-ci.com/code-intelligence)   
 
 [https://www.boussimmo.com](https://www.boussimmo.com)

Searching your property through the **email alerts** and using your **Gmail address**? Tired of wasting time opening and reading **a lot of emails** all the day? Fed up of seeing the **same properties** on different websites? Try [BoussImmo](https://www.boussimmo.com)!

x-special/nautilus-clipboard
copy
file:///home/florian/Pictures/Screenshot%20from%202021-04-28%2011-41-53.png


## Stack

- PHP 8.0
- Symfony 5.2
- jQuery 3.5
- Gmail API
- Google OAuth 2.0

## Dev

### Requirements

-  PHP 8.0.0 (or higher)
-  MySQL 8.0 (or higher) or PostgreSQL 12 (or higher)
-  Composer 2
-  Yarn
- A Google API key (create yours on [Google Cloud Platform](https://console.cloud.google.com/apis/credentials))
- A Google OAuth 2.0 client (create yours on [Google Cloud Platform](https://console.cloud.google.com/apis/credentials))
- `symfony` binary (download [here](https://symfony.com/download))

### Configuration

-   Create your own `.env.local` file by coping/pasting the `.env` file
-   In the `.env.local` file:
	- Replace the value of the `DATABASE_URL` env var with your own database URL
	- Replace the value of the `GOOGLE_API_KEY` env var with your own Google API key
	- Replace the value of the `GOOGLE_CLIENT_XXX` env vars with the credentials of your own Google OAuth2.0 client

### Installation

```
$ composer install
$ bin/console doctrine:migration:migrate
$ yarn install
$ yarn dev
```

### Usage

Start the web server:
```
$ symfony serve
```


- **Subscribe to alert emails** on property websites (Bien'Ici, LeBonCoin, Logic-Immo, Ouestfrance-immo, PAP, ParuVendu, SeLoger, SuperImmo) with your **Gmail address**
- On the homepage, **sign-in** with the same Gmail address
- Visit BoussImmo as long as it is necessary

### Tests

```
$ ./bin/phpunit
```
