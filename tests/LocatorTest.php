<?php
namespace smpl\mydi\test;

use Interop\Container\ContainerInterface;
use smpl\mydi\loader\Service;
use smpl\mydi\LoaderInterface;
use smpl\mydi\Locator;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testArraySetContainer()
    {
        $result = 123;
        $locator = new Locator();
        $mock = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $mock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($result));
        $locator['test'] = $mock;
        $this->assertSame($result, $locator['test']);
        $this->assertSame(true, isset($locator['test']));
        unset($locator['test']);
        $this->assertSame(false, isset($locator['test']));
    }

    /**
     * @param string $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testSetParams($name, $value)
    {
        $locator = new Locator();
        $locator->set($name, $value);
        $this->assertSame($value, $locator->get($name));
        $locator->delete($name);
        $this->assertSame(false, $locator->has($name));
    }

    public function testHasFromLoader()
    {
        $mockLoader = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $mockLoader
            ->expects($this->once())
            ->method('has')
            ->with($this->equalTo('magic'))
            ->willReturn(true);
        $locator = new Locator([$mockLoader]);
        $this->assertTrue($locator->has('magic'));
    }

    /**
     * Есть возможность заменить существующий контейнер на новое значение
     */
    public function testSetAddNameExist()
    {
        $locator = new Locator();
        $locator->set('test', 1);
        $locator->set('test', 2);
        $this->assertSame(2, $locator->get('test'));
    }

    /**
     * В качестве ключа может быть строка это определено в интерфейсе смотри LocatorInterface и @see https://github.com/smpl/mydi/issues/18
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameNotString()
    {
        $locator = new Locator();
        $locator->set(1, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteNotExist()
    {
        $locator = new Locator();
        $locator->delete('test');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `test`, is not defined
     */
    public function testGetNameNotExist()
    {
        $locator = new Locator();
        $locator->get('test');
    }

    /**
     * В случае если добавляется в контейнер объект с интерфейсом \smpl\mydi\test\сontainerInterface
     * должен вызываться метод get у объекта, когда у Locator вызывают get
     */
    public function testSetContainer()
    {
        $result = 123;
        $locator = new Locator();
        $mock = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $mock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($result));
        $locator->set('test', $mock);
        $this->assertSame($result, $locator->get('test'));
        $locator->delete('test');
        $this->assertSame(false, $locator->has('test'));

        $locator->set('test', function () {
            return new \stdClass();
        });
        $result = $locator->get('test');
        $this->assertSame($result, $locator->get('test'));
        $this->assertTrue($result instanceof \Closure);
    }

    /**
     * Бесконечное разрешение зависимостей #10 @see https://github.com/smpl/mydi/issues/10
     * Классный способ создать багию используя магические методы @see https://github.com/smpl/mydi/issues/13
     * @expectedException \smpl\mydi\ContainerException
     */
    public function testNotCorrectConfiguration()
    {
        $locator = new Locator();
        $locator->set('a', new Service(function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->get('b');
            return $obj;
        }));
        $locator->set('b', new Service(function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->get('a');
            return $obj;
        }));
        $locator->get('a');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLoadersInvalid()
    {
        $locator = new Locator();
        $locator->setLoaders([1]);
    }

    /**
     * @test
     * @see https://github.com/smpl/mydi/issues/22
     * @dataProvider providerValidParams
     * @param string $name
     * @param mixed $value
     */
    public function getUseLoader($name, $value)
    {
        $locator = new Locator();
        $loader = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $loader->expects($this->once())
            ->method('has')
            ->with($this->equalTo($name))
            ->will($this->returnValue(true));
        $loader->expects($this->once())
            ->method('get')
            ->with($this->equalTo($name))
            ->will($this->returnValue($value));
        /** @var LoaderInterface $loader */
        $locator->setLoaders([$loader]);
        $this->assertSame($value, $locator->get($name));
    }

    public function testGetLoader()
    {
        $locator = new Locator();
        $this->assertSame([], $locator->getLoaders());

        $result = [$this->getMockBuilder(ContainerInterface::class)->getMock()];
        /** @var LoaderInterface[] $result */
        $locator->setLoaders($result);
        $this->assertSame($result, $locator->getLoaders());
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

    /**
     * @param $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testLocatorInterfacArrayParams($name, $value)
    {
        $locator = new Locator();
        $locator[$name] = $value;
        $this->assertSame($value, $locator[$name]);
        $this->assertSame(true, isset($locator[$name]));
        unset($locator[$name]);
        $this->assertSame(false, isset($locator[$name]));
    }


    public function testLocatorInterfacArraySetNameExist()
    {
        $locator = new Locator();
        $locator['test'] = 1;
        $this->assertSame(1, $locator['test']);
        $locator['test'] = 2;
        $this->assertSame(2, $locator['test']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocatorInterfacArraySetNameNotString()
    {
        $locator = new Locator();
        $locator[1] = 1;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocatorInterfacArrayDeleteNotExist()
    {
        $locator = new Locator();
        unset($locator['test']);
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     */
    public function testLocatorInterfacArrayGetNameNotExist()
    {
        $locator = new Locator();
        $locator['test'];
    }
}
 