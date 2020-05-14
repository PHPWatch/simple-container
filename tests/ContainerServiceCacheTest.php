<?php

namespace PHPWatch\SimpleContainer\Tests;

use PHPWatch\SimpleContainer\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use function foo\func;

class ContainerServiceCacheTest extends TestCase {
    public function testStandardServiceResolutionInInit(): void {
        $container = new Container(
            [
                'foo' => fn() => bin2hex(random_bytes(16)),
            ]
        );

        $value = $container['foo'];
        $this->assertIsString($value);
        $this->assertSame($value, $container['foo']);
        $this->assertSame($value, $container['foo']);
        $this->assertSame($value, $container['foo']);
    }

    public function testStandardServiceResolution(): void {
        $container = new Container();
        $random = fn() => random_int(\PHP_INT_MIN, \PHP_INT_MAX);
        $container->set('bar', $random);

        $value = $container['bar'];
        $this->assertIsInt($value);
        $this->assertSame($value, $container['bar']);
        $this->assertSame($value, $container['bar']);

        $container->setFactory('foo', $random);
        $container->setFactory('bar');
        $this->assertNotSame($container['foo'], $container['foo']);
        $this->assertNotSame($container['bar'], $container['bar']);
    }

    public function testServiceOverrides(): void {
        $random = fn() => random_int(43, \PHP_INT_MAX);
        $static = 42;

        $container = new Container(['foo' => $random]);
        $this->assertNotSame($static, $container['foo']);

        $container->set('foo', $static);
        $this->assertSame($static, $container['foo']);
        $this->assertSame($static, $container->get('foo'));
    }

    public function testCreateFromArray(): void {
        $services = [
            'database' => [
                'dsn' => 'sqlite...'
            ],
            'prefix' => 'Foo',
            'csprng' => fn(ContainerInterface $container) => $container->get('prefix') . bin2hex(random_bytes(16)),
        ];

        $container = new Container($services);
        $this->assertStringStartsWith('Foo', $container->get('csprng'));
    }
}
