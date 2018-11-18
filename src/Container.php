<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Exception\InfiniteRecursion;
use Smpl\Mydi\Exception\NameNotString;
use Smpl\Mydi\Exception\NotFound;

class Container implements ContainerInterface
{
    private $providers;
    private $values = [];
    private $calls = [];

    public function __construct(ProviderInterface ... $providers)
    {
        $this->providers = $providers;
    }

    public function has($name): bool
    {
        return array_key_exists($name, $this->values) || null !== $this->getProviderOrNull($name);
    }

    public function get($name)
    {
        if (!is_string($name)) {
            throw new NameNotString;
        }
        if (array_search($name, $this->calls) !== false) {
            throw new InfiniteRecursion($name, $this->calls);
        }
        $this->calls[] = $name;
        $result = $this->getValue($name);
        array_pop($this->calls);
        return $result;
    }

    private function getValue(string $name)
    {
        if (!array_key_exists($name, $this->values)) {
            $provider = $this->getProvider($name);
            $this->values[$name] = $provider->provide($name);
        }

        return $this->load($this->values[$name]);
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

    private function load($result)
    {
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
            throw new NotFound($name);
        }
        return $provider;
    }
}
