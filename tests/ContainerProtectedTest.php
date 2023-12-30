<?php

namespace PHPWatch\SimpleContainer\Tests;

use PHPWatch\SimpleContainer\Container;
use PHPUnit\Framework\TestCase;

class ContainerProtectedTest extends TestCase {
    public function testProtectedFromDefinition(): void {
        $item = static fn() => 42;
        $container = new Container(['foo' => $item, 'bar' => 42]);

        $this->assertSame(42, $container['foo']);

        $container->setProtected('foo');
        $this->assertIsCallable($container['foo']);
    }
}
