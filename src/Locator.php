<?php
namespace Smpl\Mydi;

class Locator extends AbstractLocator
{
    private $containers = [];
    private $calls = [];
    private $isDependencyMapBuild = false;
    private $dependencyMap = [];

    public function resolve($name)
    {
        $this->beforeResolve($name);
        $result = $this->load($name);
        $this->afterResolve();
        return $result;
    }

    /**
     * @param $name
     */
    private function beforeResolve($name)
    {
        if ($this->isDependencyMapBuild) {
            $this->setDependencyMap($name);
        }

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

        if (!array_key_exists($name, $this->containers)) {
            $this->tryLoadFromLoader($name);
        }
    }

    public function has($name)
    {
        return array_key_exists($name, $this->containers)
        || !is_null($this->getLoaderForContainer($name));
    }

    /**
     * @param $name
     * @throw \InvalidArgumentException в случае если не сможет найти подходящий Loader
     */
    private function tryLoadFromLoader($name)
    {
        $result = $this->getLoaderForContainer($name);
        if (is_null($result)) {
            throw new \InvalidArgumentException(sprintf('Container name: `%s` is not defined', $name));
        }
        $this->set($name, $result->load($name));
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

    /**
     * @param $name
     * @return mixed
     */
    private function load($name)
    {
        $result = $this->containers[$name];
        if ($result instanceof ContainerInterface) {
            $result = $result->resolve($this);
            return $result;
        }
        return $result;
    }

    private function afterResolve()
    {
        array_pop($this->calls);
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
        $names = $this->getAllName();
        $this->isDependencyMapBuild = true;
        foreach ($names as $containerName) {
            $this->resolve($containerName);
        }
        $this->isDependencyMapBuild = false;
        return $this->dependencyMap;
    }
    private function getAllName()
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

    /**
     * @param $name
     */
    private function setDependencyMap($name)
    {
        if (empty($this->calls)) {
            $containerName = $name;
            $containerValue = [];
        } else {
            $containerName = $this->calls[count($this->calls) - 1];
            $containerValue = $name;
        }
        if (is_array($containerValue) && !array_key_exists($containerName, $this->dependencyMap)) {
            $this->dependencyMap[$containerName] = $containerValue;
        }
        if (is_string($containerValue)) {
            $this->dependencyMap[$containerName][] = $containerValue;
        }
    }
}