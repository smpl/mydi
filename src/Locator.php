<?php
namespace smpl\mydi;

use Interop\Container\ContainerInterface;

class Locator implements LocatorInterface
{
    /**
     * @var ContainerInterface[]
     */
    private $containers;
    private $values = [];
    private $calls = [];
    private $dependencyMap = [];

    /**
     * @param ContainerInterface[] $containers
     */
    public function __construct(array $containers = [])
    {
        $this->setContainers($containers);
    }

    public function get($name)
    {
        $this->updateDependencyMap($name);

        if (array_search($name, $this->calls) !== false) {
            throw new ContainerException(
                sprintf(
                    'Infinite recursion in the configuration, name called again: %s, call stack: %s. ',
                    $name,
                    implode(', ', $this->calls)
                )
            );
        }
        array_push($this->calls, $name);

        $result = $this->load($name);

        array_pop($this->calls);
        return $result;
    }

    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        $this->values[$name] = $value;
    }

    public function has($name)
    {
        return array_key_exists($name, $this->values)
        || !is_null($this->getLoaderForContainer($name));
    }


    public function delete($name)
    {
        if (!array_key_exists($name, $this->values)) {
            throw new \InvalidArgumentException(sprintf('name is not exist, %s', $name));
        }
        unset($this->values[$name]);
    }

    public function getDependencyMap()
    {
        return $this->dependencyMap;
    }

    public function setContainers(array $containers)
    {
        foreach ($containers as $loader) {
            if (!$loader instanceof ContainerInterface) {
                throw new \InvalidArgumentException('Containers array must instance of ContainerInterface');
            }
        }
        $this->containers = $containers;
    }

    public function getContainers()
    {
        return $this->containers;
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    private function getLoaderForContainer($name)
    {
        $result = null;
        foreach ($this->getContainers() as $loader) {
            if ($loader->has($name)) {
                $result = $loader;
                break;
            }
        }
        return $result;
    }

    private function updateDependencyMap($name)
    {
        $dependencyName = $this->getDependencyName($name);
        $this->prepareDependencyMap($name, $dependencyName);
        if ($name !== $dependencyName
            && !in_array($name, $this->dependencyMap[$dependencyName])
        ) {
            $this->dependencyMap[$dependencyName] = array_merge($this->dependencyMap[$dependencyName], [$name]);
        }
    }

    private function getDependencyName($name)
    {
        $result = $name;
        if (!empty($this->calls)) {
            $result = $this->calls[count($this->calls) - 1];
        }
        return $result;
    }

    private function load($name)
    {
        if (!array_key_exists($name, $this->values)) {
            $result = $this->getLoaderForContainer($name);
            if (is_null($result)) {
                throw new NotFoundException(sprintf('Container: `%s`, is not defined', $name));
            }
            $this->set($name, $result->get($name));
        }

        $result = $this->values[$name];
        if ($result instanceof LoaderInterface) {
            $result = $result->get($this);
            return $result;
        }
        return $result;
    }

    private function prepareDependencyMap($name, $dependencyName)
    {
        if (!array_key_exists($dependencyName, $this->dependencyMap)) {
            $this->dependencyMap[$dependencyName] = [];
        } else {
            if (!array_key_exists($name, $this->dependencyMap)) {
                $this->dependencyMap[$name] = [];
            }
        }
    }
}