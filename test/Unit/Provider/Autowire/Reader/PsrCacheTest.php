<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire\Reader;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Provider\Autowire\Reader\PsrCache;
use stdClass;

class PsrCacheTest extends TestCase
{

    public function testGetDependeciesMiss()
    {
        $item = $this->getMockBuilder(CacheItemInterface::class)->getMock();
        $item->expects($this->once())
            ->method('isHit')
            ->willReturn(false);
        $item->expects($this->once())
            ->method('set');
        $item->expects($this->once())
            ->method('get')
            ->willReturn(['test']);
        /** @var CacheItemInterface $item */
        $pool = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $pool->expects($this->once())
            ->method('getItem')
            ->willReturn($item);
        $pool->expects($this->once())
            ->method('save');
        /** @var CacheItemPoolInterface $pool */

        $reader = new PsrCache($pool);
        $this->assertSame(['test'], $reader->getDependecies(stdClass::class));

    }

    public function testGetDependeciesHit()
    {
        $item = $this->getMockBuilder(CacheItemInterface::class)->getMock();
        $item->expects($this->once())
            ->method('isHit')
            ->willReturn(true);
        $item->expects($this->once())
            ->method('get')
            ->willReturn(['test']);
        /** @var CacheItemInterface $item */
        $pool = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $pool->expects($this->once())
            ->method('getItem')
            ->willReturn($item);
        /** @var CacheItemPoolInterface $pool */

        $reader = new PsrCache($pool);
        $this->assertSame(['test'], $reader->getDependecies(stdClass::class));
    }

    public function testGetDependenciesInvalidCacheKey()
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $exception = new class extends Exception implements InvalidArgumentException
        {
        };
        $pool = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $pool->expects($this->once())
            ->method('getItem')
            ->willThrowException($exception);
        /** @var CacheItemPoolInterface $pool */

        (new PsrCache($pool))->getDependecies('any name');
    }

    public function testGetDependeciesInvalidName()
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $item = $this->getMockBuilder(CacheItemInterface::class)->getMock();
        $item->expects($this->once())
            ->method('isHit')
            ->willReturn(false);
        /** @var CacheItemInterface $item */
        $pool = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $pool->expects($this->once())
            ->method('getItem')
            ->willReturn($item);
        /** @var CacheItemPoolInterface $pool */

        (new PsrCache($pool))->getDependecies('invalid class name');
    }
}
