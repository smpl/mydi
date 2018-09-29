<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Smpl\Mydi\NotFoundException;
use Smpl\Mydi\ProviderInterface;

class Cached implements ProviderInterface
{
    private $provider;
    private $cache;

    public function __construct(ProviderInterface $provider, CacheInterface $cache)
    {
        $this->provider = $provider;
        $this->cache = $cache;
    }

    public function provide(string $name)
    {
        try {
            $key = "provide.$name";
            if (!$this->cache->has($key)) {
                $this->cache->set($key, $this->provider->provide($name));
            }
            return $this->cache->get($key);
        } catch (InvalidArgumentException $e) {
            throw new NotFoundException('Invalid cache key', 0, $e);
        }
    }

    public function hasProvide(string $name): bool
    {
        try {
            $key = "hasProvide.$name";
            if (!$this->cache->has($key)) {
                $this->cache->set($key, $this->provider->hasProvide($name));
            }
            $result = $this->cache->get($key);
        } catch (InvalidArgumentException $e) {
            $result = false;
        }
        return $result;
    }
}
