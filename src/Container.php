<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\Exception\ContainerException;
use Smpl\Mydi\Exception\NotFoundException;

final class Container implements ContainerInterface
{
    /**
     * @var ContainerInterface[]
     */
    private $providers;
    private $values = [];
    private $calls = [];
    private $dependencyMap = [];

    /**
     * @param ContainerInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->setProviders($providers);
    }

    private function setProviders(array $providers)
    {
        foreach ($providers as $provider) {
            if (!$provider instanceof ProviderInterface) {
                throw new \InvalidArgumentException('Providers array must instance of ContainerInterface');
            }
        }
        $this->providers = $providers;
    }

    public function get($name)
    {
        $this->checkName($name);
        $this->updateDependencyMap($name);
        $this->checkInfiniteRecursion($name);
        $result = $this->load($name);
        $this->updateCalls();
        return $result;
    }

    /**
     * @param $name
     */
    private function checkName($name)
    {
        if (!is_string($name)) {
            throw new ContainerException('Container name must be string');
        }
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

    /**
     * @param $name
     */
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

    private function load($name)
    {
        if (!array_key_exists($name, $this->values)) {
            $loader = $this->getProviderForContainer($name);
            if (is_null($loader)) {
                throw new NotFoundException(sprintf('Container: `%s`, is not defined', $name));
            }
            $this->values[$name] = $loader->get($name);
        }

        $result = $this->values[$name];
        if ($result instanceof LoaderInterface) {
            $result = $result->get($this);
            return $result;
        }
        return $result;
    }

    private function getProviderForContainer(string $name)
    {
        $result = null;
        foreach ($this->getProviders() as $provider) {
            if ($provider->has($name)) {
                $result = $provider;
                break;
            }
        }
        return $result;
    }

    private function getProviders()
    {
        return $this->providers;
    }

    private function updateCalls()
    {
        array_pop($this->calls);
    }

    public function has($name): bool
    {
        return array_key_exists($name, $this->values)
            || !is_null($this->getProviderForContainer($name));
    }

    public function getDependencyMap(): array
    {
        return $this->dependencyMap;
    }
}