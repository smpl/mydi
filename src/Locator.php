<?php
namespace smpl\mydi;

class Locator extends AbstractLocator
{
    private $containers = [];
    private $calls = [];

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

    public function isExist($name)
    {
        return array_key_exists($name, $this->containers);
    }

    /**
     * @param $name
     * @throw \InvalidArgumentException в случае если не сможет найти подходящий Loader
     */
    private function tryLoadFromLoader($name)
    {
        $result = null;
        /** @var LoaderInterface $loader */
        foreach ($this->getLoaders() as $loader) {
            if ($loader->isLoadable($name)) {
                $result = $loader;
                break;
            }
        }
        if (is_null($result)) {
            throw new \InvalidArgumentException(sprintf('Container name: `%s` is not defined', $name));
        }
        $this->add($name, $result->load($name));
    }

    public function add($name, $value)
    {
        if ($this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is already exist, %s', $name));
        }
        $this->set($name, $value);
    }

    public function set($name, $value)
    {
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
        if (!$this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is not exist, %s', $name));
        }
        unset($this->containers[$name]);
    }
}