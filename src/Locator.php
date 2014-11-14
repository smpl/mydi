<?php
namespace smpl\mydi;

use smpl\mydi\container\Service;

class Locator implements LocatorInterface
{
    private $containers = [];
    private $calls = [];

    public function resolve($name)
    {
        if (!$this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is already exist, $s', $name));
        }
        $result = $this->containers[$name];
        if (array_search($name, $this->calls) !== false) {
            throw new \InvalidArgumentException(
                sprintf('Infinite recursion in the configuration, name called again: %s, call stack: %s. ', $name, implode(', ', $this->calls)));
        }
        array_push($this->calls, $name);
        if ($result instanceof ContainerInterface) {
            $result = $result->resolve();
        }
        array_pop($this->calls);
        return $result;
    }

    public function add($name, $value)
    {
        if ($this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is already exist, %s', $name));
        }
        $this->set($name, $value);
    }

    public function delete($name)
    {
        if (!$this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is not exist, %s', $name));
        }
        unset($this->containers[$name]);
    }

    private function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        if (is_callable($value)) {
            $value = new Service($value);
        }
        $this->containers[$name] = $value;
    }

    public function isExist($name)
    {
        return isset($this->containers[$name]);
    }

    public function offsetExists($offset)
    {
        return $this->isExist($offset);
    }

    public function offsetGet($offset)
    {
        return $this->resolve($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    public function __get($name)
    {
        return $this->resolve($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
}