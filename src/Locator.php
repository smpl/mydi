<?php
namespace Smpl\Mydi;

class Locator extends AbstractLocator
{
    private $containers = [];
    private $calls = [];
    private $dependencyMap = [];

    private function getDependencyName($name)
    {
        $result = $name;
        if (!empty($this->calls)) {
            $result = $this->calls[count($this->calls) - 1];
        }
        return $result;
    }

    private function updateDependencyMap($name)
    {
        $dependencyName = $this->getDependencyName($name);
        if (!array_key_exists($dependencyName, $this->dependencyMap)) {
            $this->dependencyMap[$dependencyName] = [];
        } else if (!array_key_exists($name, $this->dependencyMap)) {
            $this->dependencyMap[$name] = [];
        }
        if ($name !== $dependencyName
            && !in_array($name, $this->dependencyMap[$dependencyName])
        ) {
            $this->dependencyMap[$dependencyName] = array_merge($this->dependencyMap[$dependencyName], [$name]);
        }
    }

    public function get($name)
    {
        $this->updateDependencyMap($name);

        if (array_search($name, $this->calls) !== false) {
            throw new \InvalidArgumentException(
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

    public function has($name)
    {
        return array_key_exists($name, $this->containers)
        || !is_null($this->getLoaderForContainer($name));
    }

    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        $this->containers[$name] = $value;
    }

    /**
     * @param string $name
     * @return null|LoaderInterface null если Loader не найден
     */
    private function getLoaderForContainer($name)
    {
        $result = null;
        /** @var LoaderInterface $loader */
        foreach ($this->getLoaders() as $loader) {
            if ($loader->isLoadable($name)) {
                $result = $loader;
                break;
            }
        }
        return $result;
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

    public function getContainers()
    {
        $result = array_keys($this->containers);
        foreach ($this->loaders as $loader) {
            $names = $loader->getLoadableContainerNames();
            foreach ($names as $name) {
                if (!in_array($name, $result)) {
                    $result[] = $name;
                }
            }
        }
        return $result;
    }

    private function load($name)
    {
        if (!array_key_exists($name, $this->containers)) {
            $result = $this->getLoaderForContainer($name);
            if (is_null($result)) {
                throw new \InvalidArgumentException(sprintf('Container name: `%s` is not defined', $name));
            }
            $this->set($name, $result->load($name));
        }

        $result = $this->containers[$name];
        if ($result instanceof ContainerInterface) {
            $result = $result->get($this);
            return $result;
        }
        return $result;
    }
}