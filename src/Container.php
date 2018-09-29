<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    private $providers;
    private $values = [];
    private $calls = [];

    public function __construct(ProviderInterface ... $providers)
    {
        foreach ($providers as $provider) {
            if ($provider instanceof ContainerAwareInterface) {
                $provider->setContainer($this);
            }
        }
        $this->providers = $providers;
    }

    public function has($name): bool
    {
        return array_key_exists($name, $this->values) || null !== $this->getProviderOrNull($name);
    }

    public function get($name)
    {
        if (!is_string($name)) {
            throw new ContainerException('Container name must be string');
        }
        $this->checkInfiniteRecursion($name);
        $result = $this->getValue($name);
        return $result;
    }

    /**
     * @param string $name
     * @throws ContainerExceptionInterface
     */
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
    }

    private function getValue(string $name)
    {
        $this->calls[] = $name;
        if (!array_key_exists($name, $this->values)) {
            $provider = $this->getProvider($name);
            $this->values[$name] = $provider->provide($name);
        }

        $result = $this->load($name);
        array_pop($this->calls);
        return $result;
    }

    private function getProviderOrNull(string $name)
    {
        $result = null;
        foreach ($this->providers as $provider) {
            if ($provider->hasProvide($name)) {
                $result = $provider;
                break;
            }
        }
        return $result;
    }

    private function load(string $name)
    {
        $result = $this->values[$name];
        if ($result instanceof LoaderInterface) {
            $result = $result->load($this);
        }
        return $result;
    }

    /**
     * @param string $name
     * @return ProviderInterface
     * @throws NotFoundExceptionInterface
     */
    private function getProvider(string $name): ProviderInterface
    {
        $provider = $this->getProviderOrNull($name);
        if (null === $provider) {
            throw new NotFoundException(sprintf('Container: `%s`, is not defined', $name));
        }
        return $provider;
    }
}
