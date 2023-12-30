<?php

namespace PHPWatch\SimpleContainer\Tests;

use PHPWatch\SimpleContainer\Container;
use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use const PHP_INT_MAX;

class ContainerFactoryTest extends TestCase {
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testFactoryReturnsDifferentValues(): void {
        $random = static fn() => random_int(43, PHP_INT_MAX);
        $container = new Container(['foo' => $random]);

        $this->assertGreaterThan(42, $container->get('foo'));
        $this->assertSame($container['foo'], $container->get('foo'));

        $container->setFactory('bar', $random);
        $this->assertNotSame($container['bar'], $container['bar']);
        $this->assertNotSame($container->get('bar'), $container->get('bar'));
    }
}
