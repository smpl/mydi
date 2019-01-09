<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire\Reader;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Smpl\Mydi\Provider\Autowire\Reader\PsrSimpleCache;
use stdClass;

class PsrSimpleCacheTest extends TestCase
{
    public function testGetDependeciesMiss()
    {
        $cache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $cache->expects($this->once())
            ->method('has')
            ->willReturn(false);
        $cache->expects($this->once())
            ->method('set');
        $cache->expects($this->once())
            ->method('get')
            ->willReturn(['test']);
        /** @var CacheInterface $cache */

        $reader = new PsrSimpleCache($cache);
        $this->assertSame(['test'], $reader->getDependecies(stdClass::class));
    }

    public function testGetDependenciesHit()
    {
        $cache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $cache->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $cache->expects($this->never())
            ->method('set');
        $cache->expects($this->once())
            ->method('get')
            ->willReturn(['test']);
        /** @var CacheInterface $cache */

        $reader = new PsrSimpleCache($cache);
        $this->assertSame(['test'], $reader->getDependecies(stdClass::class));
    }

    public function testGetDependenciesInvalidCacheKey()
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $cache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $cache->expects($this->once())
            ->method('has')
            ->willReturn(false);
        /** @var CacheInterface $cache */

        (new PsrSimpleCache($cache))->getDependecies('any name');
    }

    public function testGetDependenciesInvalidName()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $exception = new class extends Exception implements InvalidArgumentException
        {
        };
        $cache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $cache->expects($this->once())
            ->method('has')
            ->willThrowException($exception);
        /** @var CacheInterface $cache */

        (new PsrSimpleCache($cache))->getDependecies('any name');
    }
}
