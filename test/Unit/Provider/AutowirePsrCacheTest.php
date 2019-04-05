<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Smpl\Mydi\Exception\NotFoundInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\AutowirePsrCache;
use stdClass;

class AutowirePsrCacheTest extends TestCase
{
    /**
     * @var AutowirePsrCache
     */
    private $autowire;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    public function testHasProvide()
    {
        $this->assertTrue($this->autowire->hasProvide(stdClass::class));
        $this->assertFalse($this->autowire->hasProvide('invalid name'));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideMiss()
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('set')->with([]);
        $item->method('isHit')
            ->willReturn(false);
        $item->method('get')
            ->willReturn([]);
        $this->cache->method('getItem')->willReturn($item);
        $this->assertInstanceOf(Service::class, $this->autowire->provide(stdClass::class));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideHit()
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')
            ->willReturn(true);
        $item->method('get')
            ->willReturn([]);
        $this->cache->method('getItem')->willReturn($item);
        $this->assertInstanceOf(Service::class, $this->autowire->provide(stdClass::class));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideReflectionException()
    {
        $this->expectException(NotFoundInterface::class);
        $item = $this->createMock(CacheItemInterface::class);
        $this->cache->method('getItem')->willReturn($item);
        $this->autowire->provide('invalid name');
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideInvalidArgumentExceptionInCache()
    {
        $this->expectException(NotFoundInterface::class);
        $exception = new class extends Exception implements InvalidArgumentException
        {
        };
        $this->cache->method('getItem')->willThrowException($exception);
        $this->autowire->provide('invalid name');
    }

    protected function setUp()
    {
        $this->cache = $this->createMock(CacheItemPoolInterface::class);
        /** @var CacheItemPoolInterface $cache */
        $cache = $this->cache;
        $this->autowire = new AutowirePsrCache($cache);
    }

}
