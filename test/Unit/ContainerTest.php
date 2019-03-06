<?php

namespace Smpl\Mydi\Test\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Container;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\ProviderInterface;

class ContainerTest extends TestCase
{
    public function testHasFromLoader()
    {
        $mockLoader = $this->createMock(ProviderInterface::class);
        $mockLoader->expects($this->once())
            ->method('hasProvide')
            ->with($this->equalTo('magic'))
            ->willReturn(true);
        /** @var ProviderInterface $mockLoader */
        $locator = new Container($mockLoader);
        $this->assertTrue($locator->has('magic'));
    }

    public function testGetNotString()
    {
        $this->expectException(\Psr\Container\ContainerExceptionInterface::class);
        $this->expectExceptionMessage('Container name must be string');
        $locator = new Container();
        $locator->get(123);
    }

    public function testGetNameNotExist()
    {
        $this->expectException(\Psr\Container\NotFoundExceptionInterface::class);
        $this->expectExceptionMessage('Container: `test`, is not defined');
        $locator = new Container();
        $locator->get('test');
    }

    public function testSetLoader()
    {
        $result = 123;

        $loader = $this->createMock(LoaderInterface::class);
        $loader->expects($this->any())
            ->method('load')
            ->will($this->returnValue($result));
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('hasProvide')
            ->willReturn(true);
        $provider->method('provide')
            ->willReturn($loader);
        /** @var ProviderInterface $provider */
        $locator = new Container($provider);
        $this->assertSame($result, $locator->get('test'));
    }

    public function testInfiniteRecursionConfiguration()
    {
        $this->expectException(\Psr\Container\ContainerExceptionInterface::class);
        $this->expectExceptionMessage('Infinite recursion in the configuration, name called again: a, call stack: a, b.');
        $loaderA = $this->createMock(LoaderInterface::class);
        $loaderA->method('load')
            ->willReturnCallback(function (ContainerInterface $locator) {
                $obj = new \stdClass();
                $obj->test = $locator->get('b');
                return $obj;
            });

        $loaderB = $this->createMock(LoaderInterface::class);
        $loaderB->method('load')
            ->willReturnCallback(function (ContainerInterface $locator) {
                $obj = new \stdClass();
                $obj->test = $locator->get('a');
                return $obj;
            });
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('hasProvide')
            ->willReturn(true);
        $provider->method('provide')
            ->willReturnCallback(function ($name) use ($loaderA, $loaderB) {
                $result = $loaderA;
                if ($name === 'b') {
                    $result = $loaderB;
                }
                return $result;
            });
        /** @var ProviderInterface $provider */
        $locator = new Container($provider);
        $locator->get('a');
    }

    public function testSetLoadersInvalid()
    {
        $this->expectException(\TypeError::class);
        new Container([1]);
    }

    /**
     * @dataProvider providerValidParams
     * @param string $name
     * @param mixed $value
     */
    public function testGetUseLoader($name, $value)
    {

        $provider = $this->getMockBuilder(ProviderInterface::class)->getMock();
        $provider->expects($this->once())
            ->method('hasProvide')
            ->with($this->equalTo($name))
            ->will($this->returnValue(true));
        $provider->expects($this->once())
            ->method('provide')
            ->with($this->equalTo($name))
            ->will($this->returnValue($value));
        /** @var ProviderInterface $provider */
        $locator = new Container($provider);
        $this->assertSame($value, $locator->get($name));
    }

    public function providerValidParams()
    {
        return [
            ['int', 1],
            ['float', 0.5],
            ['bool', true],
            ['string', 'test'],
            ['object', new \stdClass()],
            ['null', null]
        ];
    }
}
