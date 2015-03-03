<?php
namespace smpl\mydi\tests\unit;

use smpl\mydi\container\Service;
use smpl\mydi\LoaderInterface;

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
     * @expectedExceptionMessage Name is not defined, test
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
     * @test
     * @see https://github.com/smpl/mydi/issues/22
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Loaders must imlemenent \smpl\mydi\LoaderInterface
     * @dataProvider providerLoadersInvalidArray
     * @param $array
     */
    public function setLoadersInvilidArray($array)
    {
        $this->locator->setLoaders($array);
    }

    public function providerLoadersInvalidArray()
    {
        return [
            [[null]],
            [[false]],
            [[123]],
            [['123']],
            [[new \stdClass()]],
        ];
    }

    /**
     * @test
     * @see https://github.com/smpl/mydi/issues/22
     */
    public function getLoaders()
    {
        $this->assertSame([], $this->locator->getLoaders());
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
        $this->locator->setLoaders([$loader]);
        $this->assertSame($value, $this->locator->resolve($name));
    }

    /**
     * @test
     * @depends resolveUseLoader
     */
    public function getLoaderInvalid() {
        $this->assertSame(null, $this->locator->getLoader('invalidName'));
        $loader = $this->getMock('\smpl\mydi\LoaderInterface');
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with($this->equalTo('invalidName'))
            ->will($this->returnValue(false));
        $this->locator->setLoaders([$loader]);
        $this->assertSame(null, $this->locator->getLoader('invalidName'));
    }

    /**
     * @test
     * @depends resolveUseLoader
     */
    public function getLoaderValid() {
        $loader = $this->getMock('\smpl\mydi\LoaderInterface');
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with($this->equalTo('valid'))
            ->will($this->returnValue(true));
        $this->locator->setLoaders([$loader]);
        $this->assertTrue($this->locator->getLoader('valid') instanceof LoaderInterface);
    }
}
 