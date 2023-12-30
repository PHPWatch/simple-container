<?php

namespace PHPWatch\SimpleContainer\Tests\Exception;

use PHPWatch\SimpleContainer\Container;
use PHPWatch\SimpleContainer\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase {
    public function testNotFound(): void {
        $container = new Container();
        $this->expectException(NotFoundException::class);
        $container->get('foo');
    }

    public function testNulledValuesDoNotThrow(): void {
        $container = new Container(['foo' => null]);
        $this->assertNull($container['foo']);
    }

    public function testClosureNullReturnsDoNotThrow(): void {
        $container = new Container();
        $container['foo'] = static fn(): ?string => null;
        $this->assertNull($container['foo']);
        $this->assertNull($container['foo']); // Trigger cached result

        $container['foo'] = null;
        $this->assertNull($container['foo']);
    }

    public function testUnsetTriggersException(): void {
        $container = new Container();
        $container->set('foo', null);

        $this->assertTrue(isset($container['foo']));

        unset($container['foo']);

        $this->assertFalse(isset($container['foo']));

        $this->expectException(NotFoundException::class);
        $container['foo'];
    }
}
