<?php
namespace smpl\mydi;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class AbstractLocator implements LocatorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $isDependencyMapBuild = false;
    protected $dependencyMap = [];
    /**
     * @var LoaderInterface
     */
    protected $loader;

    public function __construct(LoaderInterface $loader, LoggerInterface $logger = null)
    {
        $this->setLogger(is_null($logger) ? new NullLogger() : $logger);
        $this->setLoader($loader);
    }

    public function offsetExists($offset)
    {
        return $this->isExist($offset);
    }

    public function offsetGet($offset)
    {
        return $this->resolve($offset);
    }

    /**
     * @return LoaderInterface
     */
    public function getLoader()
    {
        $this->logger->debug('Locator {method}', ['method' => __METHOD__]);
        return $this->loader;
    }

    /**
     * @param LoaderInterface $loader
     * @throw \InvalidArgumentException
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->logger->debug('Locator {method}', ['method' => __METHOD__]);
        $this->loader = $loader;
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
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

    /**
     * @param string $name
     * @param string|array $value
     */
    protected function setDependencyMap($name, $value)
    {
        if (is_array($value) && !array_key_exists($name, $this->dependencyMap)) {
            $this->dependencyMap[$name] = $value;
        }
        if (is_string($value)) {
            $this->dependencyMap[$name][] = $value;
        }
    }

    abstract protected function getAllName();
}