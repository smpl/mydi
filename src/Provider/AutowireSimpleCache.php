<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire\ReflectionClass;
use Smpl\Mydi\ProviderInterface;

class AutowireSimpleCache implements ProviderInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function provide(string $name)
    {
        try {
            if (!$this->cache->has($name)) {
                $class = new ReflectionClass($name);
                $this->cache->set($name, $class->getConstructDependencies());
            }
            return Service::fromClassName($name, $this->cache->get($name));
        } catch (ReflectionException $e) {
            throw new NotFound($name);
        } catch (InvalidArgumentException $e) {
            throw new NotFound($name);
        }
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }
}
