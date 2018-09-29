<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Smpl\Mydi\Provider\Cached;
use Smpl\Mydi\ProviderInterface;

class CachedTest extends TestCase
{
    public function testProvideCached()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $name = 'magic';
        $cache->expects($this->once())
            ->method('has');
        $cache->expects($this->once())
            ->method('get')
            ->with("provide.$name")
            ->willReturn(123);

        /** @var CacheInterface $cache */
        /** @var ProviderInterface $provider */
        $cached = new Cached($provider, $cache);
        $this->assertSame(123, $cached->provide($name));
    }

    public function testProvideNotCached()
    {
        $name = 'magic';
        $key = "provide.$name";

        $provider = $this->createMock(ProviderInterface::class);
        $provider->expects($this->once())
            ->method('provide');

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('has')
            ->with($key)
            ->willReturn(false);
        $cache->expects($this->once())
            ->method('set');
        $cache->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(123);

        /** @var CacheInterface $cache */
        /** @var ProviderInterface $provider */
        $cached = new Cached($provider, $cache);
        $this->assertSame(123, $cached->provide($name));
    }

    public function testHasProvideCached()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $name = 'magic';
        $cache->expects($this->once())
            ->method('has');
        $cache->expects($this->once())
            ->method('get')
            ->with("hasProvide.$name")
            ->willReturn(true);

        /** @var CacheInterface $cache */
        /** @var ProviderInterface $provider */
        $cached = new Cached($provider, $cache);
        $this->assertSame(true, $cached->hasProvide($name));
    }

    public function testHasProvideNotCached()
    {
        $name = 'magic';
        $key = "hasProvide.$name";

        $provider = $this->createMock(ProviderInterface::class);
        $provider->expects($this->once())
            ->method('hasProvide');

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('has')
            ->with($key)
            ->willReturn(false);
        $cache->expects($this->once())
            ->method('set');
        $cache->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(true);

        /** @var CacheInterface $cache */
        /** @var ProviderInterface $provider */
        $cached = new Cached($provider, $cache);
        $this->assertSame(true, $cached->hasProvide($name));
    }

    public function testHasProvideInvalidArgumentException()
    {
        $exception = new class extends Exception implements InvalidArgumentException
        {
        };
        $provider = $this->createMock(ProviderInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        /** @var \Throwable $exception */
        $cache->method('has')
            ->will($this->throwException($exception));
        /** @var CacheInterface $cache */
        /** @var ProviderInterface $provider */
        $cached = new Cached($provider, $cache);
        $this->assertFalse($cached->hasProvide('magic'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testProvideInvalidArgumentException()
    {
        $exception = new class extends Exception implements InvalidArgumentException
        {
        };
        $provider = $this->createMock(ProviderInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        /** @var \Throwable $exception */
        $cache->method('has')
            ->will($this->throwException($exception));
        /** @var CacheInterface $cache */
        /** @var ProviderInterface $provider */
        $cached = new Cached($provider, $cache);
        $cached->provide('magic');
    }
}
