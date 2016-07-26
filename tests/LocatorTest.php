<?php
namespace SmplTest\Mydi;

use Smpl\Mydi\Container\Service;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\Locator;
use Smpl\Mydi\LocatorInterface;

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

    public function testHasFromLoader()
    {
        $mockLoader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $mockLoader
            ->expects($this->once())
            ->method('isLoadable')
            ->with($this->equalTo('magic'))
            ->willReturn(true)
        ;
        $locator = new Locator([$mockLoader]);
        $this->assertTrue($locator->has('magic'));
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
        $mock = $this->getMockBuilder('\Smpl\Mydi\ContainerInterface')->getMock();
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
        $loader = $this->getMockBuilder('\Smpl\Mydi\LoaderInterface')->getMock();
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

        $result = [$this->getMockBuilder(LoaderInterface::class)->getMock()];
        /** @var LoaderInterface[] $result */
        $this->locator->setLoaders($result);
        $this->assertSame($result, $this->locator->getLoaders());
    }

    public function testGetDependencyMap()
    {
        $this->locator->set('string', 'my string');
        $this->locator->set('int', 123);
        assertSame([], $this->locator->getDependencyMap());
        $expected = [
            'string' => [],
        ];
        $this->locator->resolve('string');
        $this->assertSame($expected, $this->locator->getDependencyMap());
        $this->locator->set('service', new Service(function (LocatorInterface $locator) {
            $result = new \stdClass();
            $result->string = $locator->resolve('string');
            $result->int = $locator->resolve('int');
            return $result;
        }));
        $expected += ['service' => ['string', 'int']];
        $expected += ['int' => []];
        $this->locator->resolve('service');
        $this->assertSame($expected, $this->locator->getDependencyMap());
        $this->locator->set('main', new Service(function (LocatorInterface $locator) {
            $result = new \stdClass();
            $result->service = $locator->resolve('service');
            return $result;
        }));
        $expected += ['main' => ['service']];
        $this->locator->resolve('main');
        $this->assertSame($expected, $this->locator->getDependencyMap());
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader->expects($this->never())
            ->method('getLoadableContainerNames')
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
        $this->locator->setLoaders([$loader]);
        $expected += ['loader' => ['main', 'int']];
        $this->locator->resolve('loader');
        $this->assertSame($expected, $this->locator->getDependencyMap());
    }

    public function testGetContainers()
    {
        $locator = $this->locator;
        assertSame([], $locator->getContainers());

        $locator['test'] = 123;
        $expected = ['test'];
        assertSame($expected, $locator->getContainers());

        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader
            ->method('getLoadableContainerNames')
            ->willReturn(['loader']);
            ;
        $locator->setLoaders([$loader]);
        $expected[] = 'loader';
        assertSame($expected, $locator->getContainers());

        $loader2 = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader2
            ->method('getLoadableContainerNames')
            ->willReturn(['loader', 'magic']);
        ;
        $locator->setLoaders([$loader2]);
        $expected[] = 'magic';
        assertSame($expected, $locator->getContainers());

    }
}
 