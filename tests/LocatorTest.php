<?php
namespace SmplTest\Mydi;

use Smpl\Mydi\Container\Service;
use Smpl\Mydi\LoaderInterface;

class LocatorTest extends AbstractLocator
{
    /**
     * @param string $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testSetParams($name, $value)
    {
        $this->locator->set($name, $value);
        $this->assertSame($value, $this->locator->resolve($name));
        $this->locator->delete($name);
        $this->assertSame(false, $this->locator->has($name));
    }

    /**
     * Есть возможность заменить существующий контейнер на новое значение
     */
    public function testSetAddNameExist()
    {
        $this->locator->set('test', 1);
        $this->locator->set('test', 2);
        $this->assertSame(2, $this->locator->resolve('test'));
    }

    /**
     * В качестве ключа может быть строка это определено в интерфейсе смотри LocatorInterface и @see https://github.com/smpl/mydi/issues/18
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameNotString()
    {
        $this->locator->set(1, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteNotExist()
    {
        $this->locator->delete('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container name: `test` is not defined
     */
    public function testResolveNameNotExist()
    {
        $this->locator->resolve('test');
    }

    /**
     * В случае если добавляется в контейнер объект с интерфейсом \SmplTest\Mydi\ContainerInterface
     * должен вызываться метод resolve у объекта, когда у Locator вызывают resolve
     */
    public function testSetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\Smpl\Mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator->set('test', $mock);
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->locator->delete('test');
        $this->assertSame(false, $this->locator->has('test'));

        $this->locator->set('test', function () {
            return new \stdClass();
        });
        $result = $this->locator->resolve('test');
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->assertTrue($result instanceof \Closure);
    }

    /**
     * Бесконечное разрешение зависимостей #10 @see https://github.com/smpl/mydi/issues/10
     * Классный способ создать багию используя магические методы @see https://github.com/smpl/mydi/issues/13
     * @expectedException \InvalidArgumentException
     */
    public function testNotCorrectConfiguration()
    {
        $locator = $this->locator;
        $locator->set('a', new Service(function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->resolve('b');
            return $obj;
        }));
        $locator->set('b', new Service(function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->resolve('a');
            return $obj;
        }));
        $locator->resolve('a');    // InvalidArgumentException
    }

    /**
     * @test
     * @expectedException \PHPUnit_Framework_Error
     * @dataProvider providerLoadersInvalid
     * @param $value
     */
    public function setLoadersInvalid($value)
    {
        $this->locator->setLoaders($value);
    }

    public function providerLoadersInvalid()
    {
        return [
            [null],
            [false],
            [true],
            [5],
            ['123'],
            [new \stdClass()],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLoadersInvalid()
    {
        $this->locator->setLoaders([1]);
    }

    /**
     * @test
     * @see https://github.com/smpl/mydi/issues/22
     * @dataProvider providerValidParams
     * @param string $name
     * @param mixed $value
     */
    public function resolveUseLoader($name, $value)
    {
        $loader = $this->getMock('\Smpl\Mydi\LoaderInterface');
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with($this->equalTo($name))
            ->will($this->returnValue(true));
        $loader->expects($this->once())
            ->method('load')
            ->with($this->equalTo($name))
            ->will($this->returnValue($value));
        /** @var LoaderInterface $loader */
        $this->locator->setLoaders([$loader]);
        $this->assertSame($value, $this->locator->resolve($name));
    }

    public function testGetLoader()
    {
        $this->assertSame([], $this->locator->getLoaders());

        $result = [$this->getMock(LoaderInterface::class)];
        /** @var LoaderInterface[] $result */
        $this->locator->setLoaders($result);
        $this->assertSame($result, $this->locator->getLoaders());
    }
}
 