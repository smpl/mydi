<?php
namespace smpl\mydi\test;

use smpl\mydi\container\Service;
use smpl\mydi\LoaderInterface;
use smpl\mydi\Locator;
use smpl\mydi\LocatorInterface;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    use LocatorInterfaceTestTrait;

    protected function createLoaderInterfaceObject()
    {
        $locator = new Locator();
        foreach (self::getLoadertInterfaceConfiguration() as $key => $value) {
            $locator[$key] = $value;
        }
        return $locator;
    }

    /**
     * @return LocatorInterface
     */
    protected function createLocatorInterfaceObject()
    {
       return $this->createLoaderInterfaceObject();
    }

    public function testArraySetContainer()
    {
        $result = 123;
        $locator = new Locator();
        $mock = $this->getMockBuilder('\smpl\mydi\ContainerInterface')->getMock();
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
        $mockLoader = $this->getMockBuilder(LoaderInterface::class)->getMock();
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
        $this->createLocatorInterfaceObject()->set(1, 1);
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
     * @expectedException \InvalidArgumentException
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
        $mock = $this->getMockBuilder('\smpl\mydi\ContainerInterface')->getMock();
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
     * @expectedException \InvalidArgumentException
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
        $locator->get('a');    // InvalidArgumentException
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
        $loader = $this->getMockBuilder('\smpl\mydi\LoaderInterface')->getMock();
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

        $result = [$this->getMockBuilder(LoaderInterface::class)->getMock()];
        /** @var LoaderInterface[] $result */
        $locator->setLoaders($result);
        $this->assertSame($result, $locator->getLoaders());
    }

    public function testGetDependencyMap()
    {
        $locator = new Locator();
        $locator->set('string', 'my string');
        $locator->set('int', 123);
        assertSame([], $locator->getDependencyMap());
        $expected = [
            'string' => [],
        ];
        $locator->get('string');
        $this->assertSame($expected, $locator->getDependencyMap());
        $locator->set('service', new Service(function (LocatorInterface $locator) {
            $result = new \stdClass();
            $result->string = $locator->get('string');
            $result->int = $locator->get('int');
            return $result;
        }));
        $expected += ['service' => ['string', 'int']];
        $expected += ['int' => []];
        $locator->get('service');
        $this->assertSame($expected, $locator->getDependencyMap());
        $locator->set('main', new Service(function (LocatorInterface $locator) {
            $result = new \stdClass();
            $result->service = $locator->get('service');
            return $result;
        }));
        $expected += ['main' => ['service']];
        $locator->get('main');
        $this->assertSame($expected, $locator->getDependencyMap());
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader->expects($this->never())
            ->method('getContainerNames')
            ->will($this->returnValue(['loader']));
        $loader->expects($this->once())
            ->method('has')
            ->with('loader')
            ->will($this->returnValue(true));
        $loader->expects($this->once())
            ->method('get')
            ->with('loader')
            ->will($this->returnValue(new Service(function (LocatorInterface $locator) {
                $result = new \stdClass();
                $result->main = $locator->get('main');
                $result->int = $locator->get('int');
                return $result;
            })));
        $locator->setLoaders([$loader]);
        $expected += ['loader' => ['main', 'int']];
        $locator->get('loader');
        $this->assertSame($expected, $locator->getDependencyMap());
    }

    public function testGetContainerNames()
    {
        $locator = new Locator();
        assertSame([], $locator->getContainerNames());

        $locator['test'] = 123;
        $expected = ['test'];
        assertSame($expected, $locator->getContainerNames());

        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader
            ->method('getContainerNames')
            ->willReturn(['loader']);;
        $locator->setLoaders([$loader]);
        $expected[] = 'loader';
        assertSame($expected, $locator->getContainerNames());

        $loader2 = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader2
            ->method('getContainerNames')
            ->willReturn(['loader', 'magic']);;
        $locator->setLoaders([$loader2]);
        $expected[] = 'magic';
        assertSame($expected, $locator->getContainerNames());

    }
}
 