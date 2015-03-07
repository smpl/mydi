<?php
namespace smpl\mydi;

class Locator extends AbstractLocator
{
    private $containers = [];
    private $calls = [];

    public function resolve($name)
    {
        $this->logger->debug('Locator resolve {name}', ['name' => $name]);
        $this->beforeResolve($name);
        $result = $this->load($name);
        $this->afterResolve();
        $this->logger->info('Locator container {name} is resolved', ['name' => $name]);
        return $result;
    }

    /**
     * @param $name
     */
    private function beforeResolve($name)
    {
        if ($this->isDependencyMapBuild) {
            $this->buildMap($name);
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

        if (!$this->isExist($name)) {
            $this->tryLoadFromLoader($name);
        }
    }

    /**
     * @param $name
     */
    protected function buildMap($name)
    {
        if (empty($this->calls)) {
            $containerName = $name;
            $containerValue = [];
        } else {
            $containerName = $this->calls[count($this->calls) - 1];
            $containerValue = $name;
        }
        $this->setDependencyMap($containerName, $containerValue);
    }

    public function isExist($name)
    {
        $this->logger->debug('Locator isExist container {name}', ['name' => $name]);
        return array_key_exists($name, $this->containers);
    }

    /**
     * @param $name
     * @throw \InvalidArgumentException в случае если не сможет найти подходящий Loader
     */
    private function tryLoadFromLoader($name)
    {
        if (!$this->getLoader()->isLoadable($name)) {
            throw new \InvalidArgumentException(sprintf('Container name: `%s` is not defined', $name));
        }
        $this->logger->info(
            'Locator resolve {name} with Loader {class}',
            [
                'name' => $name,
                'class' => get_class($this->getLoader())
            ]
        );
        $this->add($name, $this->getLoader()->load($name));
    }

    public function add($name, $value)
    {
        $this->logger->debug('Locator add container {name}', ['name' => $name]);
        if ($this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is already exist, %s', $name));
        }
        $this->set($name, $value);
    }

    public function set($name, $value)
    {
        $this->logger->debug('Locator set container {name}', ['name' => $name]);
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        $this->containers[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    private function load($name)
    {
        $result = $this->containers[$name];
        if ($result instanceof ContainerInterface) {
            $this->logger->info(
                'Locator use ContainerInterface {class} to resolve {name}',
                [
                    'class' => get_class($result),
                    'name' => $name
                ]
            );
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
        $this->logger->debug('Locator delete container {name}', ['name' => $name]);
        if (!$this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is not exist, %s', $name));
        }
        unset($this->containers[$name]);
    }

    protected function getAllName()
    {
        $result = array_keys($this->containers);
        $names = $this->getLoader()->getAllLoadableName();
        foreach ($names as $name) {
            if (!in_array($name, $result)) {
                $result[] = $name;
            }
        }
        return $result;
    }
}