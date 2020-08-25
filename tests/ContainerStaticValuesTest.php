<?php

namespace PHPWatch\SimpleContainer\Tests;

use Exception;
use PHPWatch\SimpleContainer\Container;
use PHPUnit\Framework\TestCase;
use stdClass;

class ContainerStaticValuesTest extends TestCase {
    public function testStringValues(): void {
        $container = new Container();
        $value = bin2hex(random_bytes(16));

        $container['foo'] = $value;
        $this->assertSame($value, $container['foo']);

        $container->set('foo', $value);
        $this->assertSame($value, $container['foo']);
    }

    public function testValuesAreOverwritten(): void {
        $container = new Container();
        $value = bin2hex(random_bytes(16));
        $value2 = bin2hex(random_bytes(16));

        $container['foo'] = $value;
        $this->assertSame($value, $container['foo']);

        $container->set('foo', $value2);
        $container['foo'] = $value2;

        $this->assertSame($value2, $container['foo']);
    }


    public function testStandardReferenceRules(): void {
        $container = new Container();
        $value = bin2hex(random_bytes(16));
        $object = new stdClass();
        $object->bar = $value;

        $container['foo-val'] = $value;
        $container['foo-obj'] = $object;

        $this->assertSame($value, $container['foo-val']);
        $this->assertSame($object, $container['foo-obj']);

        $val_obtained = $container->get('foo-val');
        $obj_obtained = $container->get('foo-obj');

        $val_obtained .= 'baz';
        $this->assertNotSame($val_obtained, $container['foo-val']);

        $obj_obtained->bar .= 'baz';
        $this->assertSame($obj_obtained, $container['foo-obj']);

        $obj_obtained_new = $container['foo-obj'];
        unset($obj_obtained_new); // Delete the reference
        $this->assertSame($object, $container->get('foo-obj'));
    }

    public function testIssetWorks(): void {
        $container = new Container(['foo' => 1, 'bar' => null]);
        $this->assertTrue(isset($container['foo']));
        $this->assertTrue(isset($container['bar'])); // Evaluate null as exists.
        $this->assertTrue($container->has('bar'));
        $this->assertFalse(isset($container['xyz']));

        $container['xyz'] = false;
        $this->assertTrue(isset($container['xyz']));
    }

    public function testIssetDoesNotExecute(): void {
        $container = new Container();
        $container->set('kill', static function() {
            throw new Exception('Must not execute');
        });

        $this->assertTrue(isset($container['kill']));

        $this->expectException(Exception::class);
        $container->get('kill');
    }
}
