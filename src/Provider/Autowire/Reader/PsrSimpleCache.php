<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire\Reader;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Provider\Autowire\AbstractReflection;
use Smpl\Mydi\Provider\Autowire\ReaderInterface;

class PsrSimpleCache implements ReaderInterface
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getDependecies(string $name): array
    {
        try {
            if (!$this->cache->has($name)) {
                $this->cache->set($name, AbstractReflection::readDependencies($name));
            }
            return $this->cache->get($name);
        } catch (InvalidArgumentException $e) {
            throw new NotFound($name);
        }
    }
}
