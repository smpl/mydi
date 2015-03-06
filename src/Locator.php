<?php
namespace smpl\mydi;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use smpl\mydi\container\Service;

class Locator implements LocatorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private $containers = [];
    private $calls = [];
    private $isDependencyMapBuild = false;
    private $dependencyMap = [];
    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

    public function __construct(array $loaders = [], LoggerInterface $logger = null)
    {
        $this->setLogger(is_null($logger) ? new NullLogger() : $logger);
        $this->setLoaders($loaders);
    }

    public function getLoader($name)
    {
        $this->logger->debug('Locator getLoader to load container {name}', ['name' => $name]);
        $result = null;
        foreach ($this->loaders as $loader) {
            if ($loader->isLoadable($name)) {
                $result = $loader;
            }
        }
        if (is_null($result)) {
            $this->logger->info('Loader not found to load {name}', ['name' => $name]);
        } else {
            $this->logger->info('Loader not found to load {name}', ['name' => $name, 'class' => get_class($result)]);
        }
        return $result;
    }

    public function resolve($name)
    {
        $this->logger->debug('Locator resolve {name}', ['name' => $name]);
        $this->beforeResolve($name);
        $result = $this->load($name);
        $this->afterResolve();
        $this->logger->info('Locator container {name} is resolved', ['name' => $name]);
        return $result;
    }

    public function add($name, $value)
    {
        $this->logger->debug('Locator add container {name}', ['name' => $name]);
        if ($this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is already exist, %s', $name));
        }
        $this->set($name, $value);
    }

    public function delete($name)
    {
        $this->logger->debug('Locator delete container {name}', ['name' => $name]);
        if (!$this->isExist($name)) {
            throw new \InvalidArgumentException(sprintf('name is not exist, %s', $name));
        }
        unset($this->containers[$name]);
    }

    public function set($name, $value)
    {
        $this->logger->debug('Locator set container {name}', ['name' => $name]);
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name must be string');
        }
        $this->containers[$name] = $value;
    }

    public function isExist($name)
    {
        $this->logger->debug('Locator isExist container {name}', ['name' => $name]);
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

    /**
     * @return LoaderInterface[]
     */
    public function getLoaders()
    {
        $this->logger->debug('Locator {method}', ['method' => __METHOD__]);
        return $this->loaders;
    }

    /**
     * @param LoaderInterface[] $loaders
     * @throw \InvalidArgumentException
     */
    public function setLoaders(array $loaders)
    {
        $this->logger->debug('Locator {method}', ['method' => __METHOD__]);
        foreach ($loaders as $loader) {
            if (!($loader instanceof LoaderInterface)) {
                throw new \InvalidArgumentException('Loaders must imlemenent \smpl\mydi\LoaderInterface');
            }
            $this->logger->info('set loader {class}', ['class' => get_class($loader)]);
        }
        $this->loaders = $loaders;
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
            $names = $loader->getAllLoadableName();
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

        if (!$this->isExist($name)) {
            $this->tryLoadFromLoader($name);
        }
    }

    private function afterResolve()
    {
        array_pop($this->calls);
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

    /**
     * @param $name
     * @throw \InvalidArgumentException в случае если не сможет найти подходящий Loader
     */
    private function tryLoadFromLoader($name)
    {
        if (is_null($loader = $this->getLoader($name))) {
            throw new \InvalidArgumentException(sprintf('Name is not defined, %s', $name));
        }
        $this->logger->info(
            'Locator resolve {name} with Loader {class}',
            [
                'name' => $name,
                'class' => get_class($loader)
            ]
        );
        $this->add($name, $loader->load($name));
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