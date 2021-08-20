# [DEPRECATED] Genly

Genly is a utility for managing a shared Docker network and other common services for local development environments. It is heavily inspired by [Dash](https://github.com/dreadfullyposh/dash).

Like [Rokanan](https://github.com/fostermadeco/rokanan), it is named after an Ursula Le Guin character.

## Prerequisites

* PHP 7.1.3
* [Composer](https://getcomposer.org/)
* [Docker Desktop](https://www.docker.com/products/docker-desktop)
* No running service listening to ports 80 or 443 on localhost

## Installation

Install Genly globally with Composer by running

```bash
composer global require fostermadeco/genly dev-master
```

Genly requires several Symfony components at version `^4.4`, because this is the current Symfony LTS version.

This means you will have to upgrade Rokanan in order to have them installed side-by-side. Run

```bash
composer global require fostermadeco/rokanan dev-require-php7
```

This will install a new version of Rokanan with the same requirements as Genly. If you require any conflicting Symfony components at ^3.4 in your root global project (as described in the [Rokanan README](https://github.com/fostermadeco/rokanan#installation), you will have to remove them first.

## Features

Genly will create its own Docker network and, upon initialization, will create a number of common Docker containers. These include,

 1. An nginx proxy, so that multiple web projects can be accessed via project-specific domain names instead of localhost.
 2. A dnsmasq resolver that will route *.test requests to the nginx proxy; this will not interfere with any *.test entries in /etc/hosts.
 3. A mailhog container that can be accessed at https://mailhog.test. Web containers should be configured to send mail to `mailhog` (the service name).
 4. A MySQL 5.7 container that can be accessed from other containers on the network at `mysql57`. It can also be accessed via MySQL clients like SequelPro or Querious at 127.0.0.1:3307.
 5. A Postgres 12 container that can be accessed from other containers on the network at `postgres`. It can also be accessed at 127.0.0.1:5433 by Postgres clients — [TablePlus](https://tableplus.com/) works well.

To be compatible with Genly, a project should contain a `docker-compose.yml` file that defines a `web` service and a `node` service. It can include any other services, but these are the minimally required ones.

## Commands

```bash
genly init
```

This command will create the `genly` network and all of the above containers.

```bash
genly init:project
```

This command will create any services defined in your project `docker-compose.yml` file.

In Docker projects, commands should only be run inside containers and not on the host machine. To make things easier, genly also has the following two convenience commands to ensure `composer` and `npm` commands are run through the `web` and `node` service, respectively. 

```bash
genly composer [...]
``` 

For example, `genly composer install --no-dev --no-scripts --classmap-authoritative` will run `composer install --no-dev --no-scripts --classmap-authoritative` in the `web` container.

```bash
genly npm [...]
```

For example, `genly npm install` will run `npm install` in the `node` container.

With Webpack and BrowserSync configured correctly, `genly npm run watch` can even be run, with live-reloading available at https://sync.example.test.

## Example docker-compose file

Here is a basic docker-compose file.

```yaml
version: '3'

services:
  web:
    image: webdevops/php-nginx:7.4
    expose:
      - 80
    working_dir: /app
    environment:
      WEB_DOCUMENT_ROOT: /app/public
      VIRTUAL_HOST: example.test
      HTTPS_METHOD: redirect
    volumes:
      - .:/app:rw,cached
    networks:
      - genly

  node:
    image: node:12
    expose:
      - 443
    working_dir: /app
    environment:
      VIRTUAL_HOST: sync.example.test
    volumes:
      - .:/app:rw,cached
    command: npm run watch
    links:
      - "web:example.test"
    networks:
      - genly

networks:
  genly:
    external:
      name: genly                 
```
