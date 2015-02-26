<?php
namespace smpl\mydi;

use smpl\mydi\container\Service;

class Locator implements LocatorInterface
{
    private $containers = [];
    private $calls = [];
    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

    public function __construct(array $loaders = [])
    {
        $this->setLoaders($loaders);
    }

    public function getLoader($name)
    {
        $result = null;
        foreach ($this->loaders as $loader) {
            if ($loader->isLoadable($name)) {
                $result = $loader;
            }
        }
        return $result;
    }

    public function resolve($name)
    {
        if (!$this->isExist($name)) {
            if (is_null($loader = $this->getLoader($name))) {
                throw new \InvalidArgumentException(sprintf('Name is not defined, %s', $name));
            }
            $this->add($name, $loader->load($name));
        }
        $result = $this->containers[$name];
        if (array_search($name, $this->calls) !== false) {
            throw new \InvalidArgumentException(
                sprintf('Infinite recursion in the configuration, name called again: %s, call stack: %s. ', $name,
                    implode(', ', $this->calls)));
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

    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        if (is_callable($value)) {
            $value = new Service($value);
        }
        $this->containers[$name] = $value;
    }

    public function __call($name, $arguments)
    {
        if ($this->isExist($name)) {
            $container = $this->resolve($name);
            if (is_callable($container)) {
                return call_user_func_array($container, $arguments);
            }
            return $container;
        }
        throw new \InvalidArgumentException;
    }

    public function isExist($name)
    {
        return array_key_exists($name, $this->containers);
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

    /**
     * @return LoaderInterface[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param LoaderInterface[] $loaders
     * @throw \InvalidArgumentException
     */
    public function setLoaders(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!($loader instanceof LoaderInterface)) {
                throw new \InvalidArgumentException('Loaders must imlemenent \smpl\mydi\LoaderInterface');
            }
        }
        $this->loaders = $loaders;
    }
}