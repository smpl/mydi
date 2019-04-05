<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Smpl\Mydi\Exception\NotFoundInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\AutowirePsrSimpleCache;
use stdClass;

class AutowirePsrSimpleCacheTest extends TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;
    /**
     * @var AutowirePsrSimpleCache
     */
    private $autowire;

    public function testHasProvide()
    {
        $this->assertTrue($this->autowire->hasProvide(stdClass::class));
        $this->assertFalse($this->autowire->hasProvide('invalid name'));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideHit()
    {
        $this->cache->method('has')->willReturn(true);
        $this->cache->method('get')->willReturn([]);
        $this->assertInstanceOf(Service::class, $this->autowire->provide(stdClass::class));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideMiss()
    {
        $this->cache->method('has')->willReturn(false);
        $this->cache->expects($this->once())->method('set')->with(stdClass::class, []);
        $this->cache->method('get')->willReturn([]);
        $this->assertInstanceOf(Service::class, $this->autowire->provide(stdClass::class));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideReflectionException()
    {
        $this->expectException(NotFoundInterface::class);
        $this->autowire->provide('invalid name');
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideInvalidArgumentExceptionInCache()
    {
        $exception = new class extends Exception implements InvalidArgumentException
        {
        };
        $this->cache->method('has')->willThrowException($exception);
        $this->expectException(NotFoundInterface::class);
        $this->autowire->provide(stdClass::class);
    }

    protected function setUp()
    {
        $this->cache = $this->createMock(CacheInterface::class);
        /** @var CacheInterface $cache */
        $cache = $this->cache;
        $this->autowire = new AutowirePsrSimpleCache($cache);
    }
}
