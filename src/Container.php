<?php

namespace PHPWatch\SimpleContainer;

use ArrayAccess;
use PHPWatch\SimpleContainer\Exception\BadMethodCallException;
use PHPWatch\SimpleContainer\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use function array_key_exists;
use function is_callable;
use function sprintf;

class Container implements ArrayAccess, ContainerInterface {
    private array $definitions;
    private array $generated = [];
    private array $protected = [];
    private array $factories = [];

    public function __construct(array $definitions = []) {
        $this->definitions = $definitions;
    }

    private function getService(string $id) {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('Container key "%s" is not defined', $id));
        }

        if (!is_callable($this->definitions[$id]) || isset($this->protected[$id])) {
            return $this->definitions[$id];
        }

        if (array_key_exists($id, $this->generated)) {
            return $this->generated[$id];
        }

        $return = $this->definitions[$id]($this);

        if (isset($this->factories[$id])) {
            return $return;
        }

        return $this->generated[$id] = $return;
    }

    public function set(string $id, $value): void {
        $this->definitions[$id] = $value;
        unset($this->generated[$id]);
    }

    public function setProtected(string $id, ?callable $value = null): void {
        if ($value === null) {
            $value = $this->getDefaultDefinition($id, sprintf('Attempt to set container ID "%s" as protected, but it is not already set nor provided in the function call.', $id));
        }

        $this->protected[$id] = true;
        $this->set($id, $value);
    }

    public function setFactory(string $id, ?callable $value = null): void {
        if ($value === null) {
            $value = $this->getDefaultDefinition($id, sprintf('Attempt to set container ID "%s" as factory, but it is not already set nor provided in the function call', $id));
        }

        $this->factories[$id] = true;
        $this->set($id, $value);
    }

    private function getDefaultDefinition(string $id, string $exception_message): callable {
        if (!$this->has($id)) {
            throw new BadMethodCallException($exception_message);
        }
        return $this->definitions[$id];
    }

    public function offsetSet($id, $value): void {
        $this->set($id, $value);
    }

    public function offsetUnset($id): void {
        unset($this->definitions[$id], $this->generated[$id], $this->factories[$id], $this->protected[$id]);
    }

    public function offsetExists($id): bool {
        return array_key_exists($id, $this->definitions);
    }

    public function offsetGet($id) {
        return $this->getService($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id) {
        return $this->getService($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool {
        return array_key_exists($id, $this->definitions);
    }
}
