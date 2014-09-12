# AsyncTasksBundle #

## About ##

The AsyncTasksBundle allows to send asynchronous messages in your Symfony2 Application via [RabbitMq](http://www.rabbitmq.com/) (using the [php-amqplib](http://github.com/videlalvaro/php-amqplib) library).

## PUBLISHING ##
The message **publishing** is so easy.
-  Create an async event ``Trt\AsyncTasksBundle\Event\AsyncEvent``
-  Dispatch it!
-  Done

```php
    $event = new AsyncEvent(
        'async_event_name',
        array('date'=> (new \DateTime())->format('d-m-Y H:s')
        )
    );
    $this->get('event_dispatcher')->dispatch($event->getName(), $event);
```

## CONSUMING ##
Now if you want **consume** the messages
-  Write your domain specific service implementing the ``Trt\AsyncTasksBundle\Listener\ListenerInterface`` interface.
-  Tag the service with ``{name: trt_async.listener.listen, event: async_event_name} ``, the *event* key is the event name.
-  Run

```bash
$ app/console trt:async:run async_event_name
```

All the examples expect a running RabbitMQ server.
## Installation ##

This instructions have been tested on a project created with the [Symfony2 Standard 2.3.4](http://symfony.com/download)

Add the AsyncTasksBundle to your composer.json and type composer install.
```json
    "require": {
        "php": ">=5.3.3",
        ....
        "trt/async-tasks-bundle": "dev-master",
        ...
    },
```
Add the AsyncTasksBundle to your application's kernel:

```php
public function registerBundles()
{
    $bundles = array(
        ...
        new Trt\AsyncTasksBundle\TrtAsyncTasksBundle(),
        ...
    );
    ...
}
```

## Configuration ##

```yaml
trt_async_tasks:
    # This prefix allows the dispatcher to detect async event,
    # if you want use another prefix put here
    event:
        prefix: async_
    mq:
        # The rabbitMq host / port
        connection_params:
            host: %mq_host%
            port: %mq_port%
        # Define the exchange name for rabbitmq
        exchange:
            exchange: 'exchange_symfony_events'
```

## Naming Conventions ##

AsyncTasksBundle's event detection mechanism is based on naming convention, the event name will be the same of queue name. every event with name containing the `async` string will be detected as AysncEvent.

The name prefix cold be overridden via config
```yaml
trt_async_tasks:
    event:
        prefix: acme_async
```
## This bundle will be shown at SymfonyDayIt  2013 (Rome) ##

Fork it and contribute to solve the issues :-)

## License ##

See: resources/meta/LICENSE.md

## Credits ##

The bundle structure and the documentation is partially based on the [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle)
