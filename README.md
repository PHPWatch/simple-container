
# Simple Container  
A fast and minimal PSR-11 compatible Dependency Injection Container with array-syntax and without auto-wiring.  
  
 
[![Latest Stable Version](https://poser.pugx.org/phpwatch/simple-container/v/stable)](https://packagist.org/packages/phpwatch/simple-container) ![CI](https://github.com/PHPWatch/simple-container/workflows/CI/badge.svg?branch=master) [![codecov](https://codecov.io/gh/PHPWatch/simple-container/branch/master/graph/badge.svg)](https://codecov.io/gh/PHPWatch/simple-container) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PHPWatch/simple-container/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PHPWatch/simple-container/?branch=master) [![License](https://poser.pugx.org/phpwatch/simple-container/license)](https://packagist.org/packages/phpwatch/simple-container)


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
  
*Simple Container* supports array-syntax for setting and fetching of services and values. You can mark certain services as factory or protected later too.  
  
### Declare services and values
  
```php
<?php
use Psr\Container\ContainerInterface;

$container = new PHPWatch\SimpleContainer\Container();  
  
$container['database.dsn'] = 'mysql:host=localhost;database=test';  
$container['database'] = static function(ContainerInterface $container): \PDO {  
 return new \PDO($container->get('database.dsn'));  
};
$container['api.ipgeo'] = 'rhkg3...';
```  

### Fetch services  
  
Do not use this class as a service locator. The closures you declare for each service will get the `Container` instance, from which you can fetch services using the array syntax:  
  
```php
<?php  
$container['database']; // \PDO  
// OR  
$container->get('database'); // \PDO  
```  
  
### Create container from definitions  
  
```php  
<?php
use Psr\Container\ContainerInterface;  
use PHPWatch\SimpleContainer\Container;  
  
$services = [  
    'database' => [
        'dsn' => 'sqlite...'  
    ],  
    'prefix' => 'Foo',  
    'csprng' => static function (ContainerInterface $container) {  
        return $container->get('prefix') . bin2hex(random_bytes(16));  
    }
]; 
  
$container = new Container($services);  
$container->get('prefix'); // Foo
```  

### Factory and Protected Services

If the service definition is a closure (similar to the `database` example above), the return value will be cached, and returned for subsequent calls without instantiating again. This is often the use expected behavior for databases and other reusable services. 

#### Factory Services

To execute the provided closure every-time a service is requested (for example, to return an HTTP client), you can use *factories*. 

```php
<?php
$container->setFactory('http.client', static function(ContainerInterface $container) {
	$handler = new curl_init();
	curl_setopt($handler,CURLOPT_USERAGENT, $container->get('http.user-agent'));
	
	return $handler;
};
```

The example above will always return a **new** curl handler resource everytime `$container->get('http.client')` is called, with the User-Agent string set to the `http.user-agent` value from container.

You can also mark a container service as a factory method later if it's already set:

```php
<?php
$container->setFactory('http.client'); // Mark existing definition as a factory.
```

If you have already declared the `http.client` service, it will now be marked as factory. If the existing declaration is not set, or is not a `callable`, a `PHPWatch\SimpleContainer\Exception\BadMethodCallExceptionTest` exception will be thrown.

#### Protected Services

Simple Container expects the service declarations to be closures, and it will execute the closure by itself to return the service. However, in some situations, you need to return a closure itself as the service. 

```php
<?php
$container['csprng'] = static function(): string {
	return bin2hex(random_bytes(32));
};

$container['csprng']; // "eaa3e95d4102..."
$container['csprng']; // "eaa3e95d4102..."
$container['csprng']; // "eaa3e95d4102..."
```

This behavior is probably not what you wanted. You can mark the service as **factory** to retrieve different values every-time it is called. You can also mark it as **protected**, which will return the **closure itself**, so you can call it on your code:

```php
<?php
$container->setProtected('csprng', static function(): string {
	return bin2hex(random_bytes(32));
});

$csprng = $container->get('csprng');

echo $csprng(); // eaa3e95d4102...
echo $csprng(); // b857ce87400b...
echo $csprng(); // a833e3db880...
```

---

### Extend container  
  
Just use the array syntax and add/remove services  
  
```php
<?php
// Remove:  
unset($container['secret.service']);  
  
// Extend:  
$container['secret.service'] = static function(): void { throw new \Exception('You are not allowed to use this');}  
```  
  
  ---
  
### Freezing container  
  
By design, container is not allowed to be frozen. The `Container` class is extensible (`get`, `getOffset`, or the `Container` class itself are not declared final) if you absolutely need this feature.

## Contributing
You are welcome to raise an issue or a PR if you have any questions or suggestions. Please keep in mind that this container aims to be the simplest and fastest container, and follow SOLID principles. Any feature that steps outside these goals (see design goals above) will likely not be accepted. However, your contributions will be appreciated and considered regardless of their nature. 

## Credits and Inspiration
This project is inspired by the Pimple project. It is no closed for new features and modifications, and does not support PSR-11. This sparked the idea of this project, and Pimple deserves the credit for its solid architecture and minimal set of features, including the support for array syntax. 

Although separate projects, Simple Container is largely compatible with Pimple. This project is used in [PHP.Watch](https://php.watch) and drop-in replaced Pimple.
