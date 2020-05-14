# Simple Container
A PSR-11 compatible Container that works similar to `pimple/pimple` but lives up to "simple" name.

## Design goals

 - Do one thing and do it well
 - ~100 LOC
 - **No auto-wiring** by design
 - Services are declared as closures
 - Array-syntax to access and set services (`$container['database']`)
 - Fully PSR-11 compliant
 - Support for protected services (return closures verbatim)
 - Support for factory services (return a new instance instead of returning the same instance)
 - 100% test coverage
 - Services can over overwritten later, marked factory/protected
 - Function at full speed without a compilation step

## Installation

```bash
composer install phpwatch/simple-container
```
## Usage

Simple Container supports array-syntax for setting and fetching services and values. You can mark certain services as factory or protected later too.

### Declare services

```php
$container = new PHPWatch\SimpleContainer\Container();


$container['database.dsn'] = 'mysql:host=localhost;database=test';
$container['database'] = static function(Container $container): \PDO {
	return new PDO($container['database.dsn');
}
```
// Todo: extend this

### Fetch services

Do not use this class as a service locator. The closures you declare for each service will get the `Container` instance, from which you can fetch services using the array syntax:

```php
$container['database'];// \PDO
// OR
$container->get('database'); // \PDO
```

### Create container from definitions

```php

use Psr\Container\ContainerInterface;
use PHPWatch\SimpleContainer\Container;

$services = [
    'database' => [
        'dsn' => 'sqlite...'
    ],
    'prefix' => 'Foo',
    'csprng' => fn(ContainerInterface $container) => $container->get('prefix') . bin2hex(random_bytes(16)),
];

$container = new Container($services);

```
### Extend container

Just use the array syntax and add/remove services

```php
// Remove:
unset($container['secret.service']);

// Extend:
$container['secret.service'] = static function(): void {
	throw new \Exception('You are not allowed to use this');
}
```

### Freezing container

By design, container is not allowed to be frozen. The `Container` class is extensible (`get`, `getOffset`, or the `Container` class itself are not declared final) if you absolutely need this feature.

