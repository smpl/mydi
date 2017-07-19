<?php

namespace Smpl\Mydi;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\Exception\ContainerException;
use Smpl\Mydi\Exception\NotFoundException;

final class Container implements ContainerInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;
    private $values = [];
    private $calls = [];
    private $dependencyMap = [];

    public function __construct(ProviderInterface ... $providers)
    {
        $this->providers = $providers;
    }

    public function get($name)
    {
        if (!is_string($name)) {
            throw new ContainerException('Container name must be string');
        }
        $this->updateDependencyMap($name);
        $this->checkInfiniteRecursion($name);
        $result = $this->load($name);
        array_pop($this->calls);
        return $result;
    }

    public function has($name)
    {
        return array_key_exists($name, $this->values)
            || !is_null($this->getLoaderForContainer($name));
    }

    public function getDependencyMap()
    {
        return $this->dependencyMap;
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

    private function load($name)
    {
        if (!array_key_exists($name, $this->values)) {
            $loader = $this->getLoaderForContainer($name);
            if (is_null($loader)) {
                throw new NotFoundException(sprintf('Container: `%s`, is not defined', $name));
            }
            $this->values[$name] = $loader->get($name);
        }

        $result = $this->values[$name];
        if ($result instanceof LoaderInterface) {
            $result = $result->get($this);
        }
        return $result;
    }

    private function getLoaderForContainer($name)
    {
        $result = null;
        foreach ($this->providers as $loader) {
            if ($loader->has($name)) {
                $result = $loader;
                break;
            }
        }
        return $result;
    }

    private function checkInfiniteRecursion($name)
    {
        if (array_search($name, $this->calls) !== false) {
            throw new ContainerException(
                sprintf(
                    'Infinite recursion in the configuration, name called again: %s, call stack: %s.',
                    $name,
                    implode(', ', $this->calls)
                )
            );
        }
        $this->calls[] = $name;
    }
}