<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire\Reader;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Provider\Autowire\AbstractReflection;
use Smpl\Mydi\Provider\Autowire\ReaderInterface;

class PsrCache implements ReaderInterface
{
    private $pool;

    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }

    public function getDependecies(string $name): array
    {
        try {
            $item = $this->pool->getItem($name);
            if (!$item->isHit()) {
                $item->set(AbstractReflection::readDependencies($name));
                $this->pool->save($item);
            }
            return $item->get();
        } catch (InvalidArgumentException $e) {
            throw new NotFound($name);
        } catch (ReflectionException $e) {
            throw new NotFound($name);
        }
    }
}
