<?php
namespace smpl\mydi;

use Interop\Container\ContainerInterface;

class Locator implements LocatorInterface
{
    /**
     * @var LoaderInterface[]
     */
    protected $loaders;
    private $containers = [];
    private $calls = [];
    private $dependencyMap = [];

    /**
     * @param LoaderInterface[] $loader
     */
    public function __construct(array $loader = [])
    {
        $this->setLoaders($loader);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function has($name)
    {
        return array_key_exists($name, $this->containers)
        || !is_null($this->getLoaderForContainer($name));
    }

    /**
     * @param string $name
     * @return null|ContainerInterface null если Loader не найден
     */
    private function getLoaderForContainer($name)
    {
        $result = null;
        /** @var ContainerInterface $loader */
        foreach ($this->getLoaders() as $loader) {
            if ($loader->has($name)) {
                $result = $loader;
                break;
            }
        }
        return $result;
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
            if (!$loader instanceof ContainerInterface) {
                throw new \InvalidArgumentException('Containers array must instance of ContainerInterface');
            }
        }
        $this->loaders = $loaders;
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
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

    private function updateDependencyMap($name)
    {
        $dependencyName = $this->getDependencyName($name);
        if (!array_key_exists($dependencyName, $this->dependencyMap)) {
            $this->dependencyMap[$dependencyName] = [];
        } else {
            if (!array_key_exists($name, $this->dependencyMap)) {
                $this->dependencyMap[$name] = [];
            }
        }
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
        if (!array_key_exists($name, $this->containers)) {
            $result = $this->getLoaderForContainer($name);
            if (is_null($result)) {
                throw new NotFoundException(sprintf('Container: `%s`, is not defined', $name));
            }
            $this->set($name, $result->get($name));
        }

        $result = $this->containers[$name];
        if ($result instanceof LoaderInterface) {
            $result = $result->get($this);
            return $result;
        }
        return $result;
    }

    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        $this->containers[$name] = $value;
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    public function delete($name)
    {
        if (!array_key_exists($name, $this->containers)) {
            throw new \InvalidArgumentException(sprintf('name is not exist, %s', $name));
        }
        unset($this->containers[$name]);
    }

    public function getDependencyMap()
    {
        return $this->dependencyMap;
    }
}