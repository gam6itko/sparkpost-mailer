# SparkPost transport for Symfony Mailer
[![Build Status](https://travis-ci.com/gam6itko/sparkpost-mailer.svg?branch=master)](https://travis-ci.com/gam6itko/sparkpost-mailer)
[![Coverage Status](https://coveralls.io/repos/github/gam6itko/sparkpost-mailer/badge.svg?branch=master)](https://coveralls.io/github/gam6itko/sparkpost-mailer?branch=master)

## install

```bash
composer require gam6itko/sparkpost-mailer
```

## configuration

### services.yaml
```yaml
services:
    mailer.transport_factory.sparkpost:
        class: Gam6itko\Symfony\Mailer\SparkPost\Transport\SparkPostTransportFactory
        arguments: ['@event_dispatcher', '@?http_client', '@?monolog.logger']
        tags:
            - {name: mailer.transport_factory}
```

### .env

#### Default region

```dotenv
MAILER_DSN=sparkpost+api://api_key@default
```
```dotenv
MAILER_DSN=sparkpost+smtp://user:password@default:port
```

#### EU region

```dotenv
MAILER_DSN=sparkpost+api://api_key@default?region=eu
```
```dotenv
MAILER_DSN=sparkpost+smtp://user:password@default:port?region=eu
```

## tests

### Using sink server 
[About sink server](https://www.sparkpost.com/docs/faq/using-sink-server/)

```yaml
services:
    Gam6itko\Symfony\Mailer\SparkPost\EventListener\SinkEnvelopeListener:
        tags: ['kernel.event_subscriber']
```

## Also

[Api transmissions](https://developers.sparkpost.com/api/transmissions/)
