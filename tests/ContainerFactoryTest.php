<?php

namespace PHPWatch\SimpleContainer\Tests;

use PHPWatch\SimpleContainer\Container;
use PHPUnit\Framework\TestCase;

class ContainerFactoryTest extends TestCase {
    public function testFactoryReturnsDifferentValues(): void {
        $random = fn() => random_int(43, \PHP_INT_MAX);
        $container = new Container(['foo' => $random]);

        $this->assertGreaterThan(42, $container->get('foo'));
        $this->assertSame($container['foo'], $container->get('foo'));

        $container->setFactory('bar', $random);
        $this->assertNotSame($container['bar'], $container['bar']);
        $this->assertNotSame($container->get('bar'), $container->get('bar'));
    }
}
