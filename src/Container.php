<?php
declare(strict_types=1);

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

    /**
     * @param ProviderInterface[] ...$providers
     */
    public function __construct(ProviderInterface ... $providers)
    {
        $this->providers = $providers;
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

    private function updateDependencyMap(string $name)
    {
        $dependencyName = $this->getDependencyName($name);
        $this->prepareDependencyMap($name, $dependencyName);
        if ($name !== $dependencyName
            && !in_array($name, $this->dependencyMap[$dependencyName])
        ) {
            $this->dependencyMap[$dependencyName] = array_merge($this->dependencyMap[$dependencyName], [$name]);
        }
    }

    private function getDependencyName(string $name): string
    {
        $result = $name;
        if (!empty($this->calls)) {
            $result = $this->calls[count($this->calls) - 1];
        }
        return $result;
    }

    private function prepareDependencyMap(string $name, string $dependencyName)
    {
        if (!array_key_exists($name, $this->dependencyMap)) {
            $this->dependencyMap[$name] = [];
        }
    }

    private function checkInfiniteRecursion(string $name)
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

    private function load(string $name)
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
        }
        return $result;
    }

    private function getProviderForContainer(string $name)
    {
        $result = null;
        foreach ($this->providers as $provider) {
            if ($provider->has($name)) {
                $result = $provider;
                break;
            }
        }
        return $result;
    }
}