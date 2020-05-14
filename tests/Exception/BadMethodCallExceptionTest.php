<?php

namespace PHPWatch\SimpleContainer\Tests\Exception;

use PHPWatch\SimpleContainer\Container;
use PHPWatch\SimpleContainer\Exception\BadMethodCallException;
use PHPUnit\Framework\TestCase;

class BadMethodCallExceptionTest extends TestCase {
    public function testMarkProtectedWithoutClosure(): void {
        $container = new Container();
        $this->expectException(BadMethodCallException::class);
        $container->setProtected('foo');
    }

    public function testMarkFactorydWithoutClosure(): void {
        $container = new Container();
        $this->expectException(BadMethodCallException::class);
        $container->setFactory('foo');
    }

    public function testMarkStaticAsFactory(): void {
        $container = new Container(['foo' => 115628]);
        $this->expectException(BadMethodCallException::class);
        $container->setFactory('foo');
    }

    public function testMarkStaticAsProtected(): void {
        $container = new Container(['foo' => 115628]);
        $this->expectException(BadMethodCallException::class);
        $container->setProtected('foo');
    }
}
