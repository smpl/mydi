<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire\ReflectionClass;
use Smpl\Mydi\ProviderInterface;

class AutowirePsrCache implements ProviderInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $pool;

    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }

    public function provide(string $name)
    {
        try {
            $item = $this->pool->getItem($name);
            if (!$item->isHit()) {
                $class = new ReflectionClass($name);
                $item->set($class->getConstructDependencies());
                $this->pool->save($item);
            }
            return Service::fromClassName($name, $item->get());
        } catch (InvalidArgumentException $e) {
            throw new NotFound($name);
        } catch (ReflectionException $e) {
            throw new NotFound($name);
        }
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }
}
