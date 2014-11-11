<?php
namespace smpl\mydi;

class Locator implements LocatorInterface
{
    private $containers = [];

    public function resolve($name)
    {
        if (!isset($this->containers[$name])) {
            throw new \InvalidArgumentException(sprintf('name is already exist, $s', $name));
        }
        $result = $this->containers[$name];
        if ($result instanceof ContainerInterface) {
            $result = $result->resolve();
        }
        return $result;
    }

    public function add($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        if (isset($this->containers[$name])) {
            throw new \InvalidArgumentException(sprintf('name is already exist, %s', $name));
        }
        $this->containers[$name] = $value;
    }
}