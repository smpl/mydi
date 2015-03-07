<?php
namespace smpl\mydi\tests\unit;

use smpl\mydi\container\Service;
use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

class LocatorTest extends AbstractLoaderTest
{
    /**
     * @param string $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testAddParams($name, $value)
    {
        $this->locator->add($name, $value);
        $this->assertSame($value, $this->locator->resolve($name));
        $this->locator->delete($name);
        $this->assertSame(false, $this->locator->isExist($name));
    }

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
        $this->assertSame(false, $this->locator->isExist($name));
    }

    /**
     * Нет возможности создать два контейнера с одинаковым именем
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameExist()
    {
        $this->locator->add('test', 1);
        $this->locator->add('test', 1);
    }

    /**
     * Есть возможность заменить существующий контейнер на новое значение
     */
    public function testSetAddNameExist()
    {
        $this->locator->add('test', 1);
        $this->locator->set('test', 2);
        $this->assertSame(2, $this->locator->resolve('test'));
    }

    /**
     * В качестве ключа может быть строка это определено в интерфейсе смотри src/LocatorInterface.php и @see https://github.com/smpl/mydi/issues/3
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameNotString()
    {
        $this->locator->add(1, 1);
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
     * В случае если добавляется в контейнер объект с интерфейсом \smpl\mydi\tests\unit\tests\unit\ContainerInterface
     * должен вызываться метод resolve у объекта, когда у Locator вызывают resolve
     */
    public function testAddContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator->add('test', $mock);
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->locator->delete('test');
        $this->assertSame(false, $this->locator->isExist('test'));

        $this->locator->add('test', function () {
            return new \stdClass();
        });
        $result = $this->locator->resolve('test');
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->assertTrue($result instanceof \Closure);
    }

    /**
     * В случае если добавляется в контейнер объект с интерфейсом \smpl\mydi\tests\unit\ContainerInterface
     * должен вызываться метод resolve у объекта, когда у Locator вызывают resolve
     */
    public function testSetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator->set('test', $mock);
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->locator->delete('test');
        $this->assertSame(false, $this->locator->isExist('test'));

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
        $locator->add('a', new Service(function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->resolve('b');
            return $obj;
        }));
        $locator->add('b', new Service(function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->resolve('a');
            return $obj;
        }));
        $locator->resolve('a');    // InvalidArgumentException
    }

    /**
     * @test
     * @see https://github.com/smpl/mydi/issues/22
     * @expectedException \PHPUnit_Framework_Error
     * @dataProvider providerLoadersInvalid
     * @param $value
     */
    public function setLoadersInvalid($value)
    {
        $this->locator->setLoader($value);
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
     * @test
     * @see https://github.com/smpl/mydi/issues/22
     * @dataProvider providerValidParams
     * @param string $name
     * @param mixed $value
     */
    public function resolveUseLoader($name, $value)
    {
        $loader = $this->getMock('\smpl\mydi\LoaderInterface');
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with($this->equalTo($name))
            ->will($this->returnValue(true));
        $loader->expects($this->once())
            ->method('load')
            ->with($this->equalTo($name))
            ->will($this->returnValue($value));
        /** @var LoaderInterface $loader */
        $this->locator->setLoader($loader);
        $this->assertSame($value, $this->locator->resolve($name));
    }

    public function testGetLoader()
    {
        $this->assertInstanceOf(LoaderInterface::class, $this->locator->getLoader());

        $result = $this->getMock(LoaderInterface::class);
        /** @var LoaderInterface $result */
        $this->locator->setLoader($result);
        $this->assertSame($result, $this->locator->getLoader());
    }

    public function testGetDependencyMap()
    {
        $loader = $this->getMock('\smpl\mydi\LoaderInterface');
        $loader->expects($this->any())
            ->method('getAllLoadableName')
            ->will($this->returnValue([]));
        /** @var LoaderInterface $loader */
        $this->locator->setLoader($loader);

        $this->locator->add('string', 'my string');
        $this->locator->add('int', 123);

        $expected = [
            'string' => [],
            'int' => []
        ];
        $this->assertSame($expected, $this->locator->getDependencyMap());

        $this->locator->add('service', new Service(function (LocatorInterface $locator) {
            $result = new \stdClass();
            $result->string = $locator->resolve('string');
            $result->int = $locator->resolve('int');
            return $result;
        }));
        $expected += ['service' => ['string', 'int']];
        $this->assertSame($expected, $this->locator->getDependencyMap());

        $this->locator->add('main', new Service(function (LocatorInterface $locator) {
            $result = new \stdClass();
            $result->service = $locator->resolve('service');
            return $result;
        }));
        $expected += ['main' => ['service']];
        $this->assertSame($expected, $this->locator->getDependencyMap());

        $loader = $this->getMock(LoaderInterface::class);
        /** @var \PHPUnit_Framework_MockObject_MockObject $loader */
        $loader->expects($this->once())
            ->method('getAllLoadableName')
            ->will($this->returnValue(['loader']));
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with('loader')
            ->will($this->returnValue(true));
        $loader->expects($this->once())
            ->method('load')
            ->with('loader')
            ->will($this->returnValue(new Service(function (LocatorInterface $locator) {
                $result = new \stdClass();
                $result->main = $locator->resolve('main');
                $result->int = $locator->resolve('int');
                return $result;
            })));
        /** @var LoaderInterface $loader */
        $this->locator->setLoader($loader);
        $expected += ['loader' => ['main', 'int']];
        $this->assertSame($expected, $this->locator->getDependencyMap());
    }
}
 