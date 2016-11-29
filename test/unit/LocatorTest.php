<?php
namespace smpl\mydi\test\unit;

use Interop\Container\ContainerInterface;
use smpl\mydi\loader\Service;
use smpl\mydi\LoaderInterface;
use smpl\mydi\Locator;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
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

    public function testSetReplace()
    {
        $locator = new Locator();
        $locator->set('test', 1);
        $locator->set('test', 2);
        $this->assertSame(2, $locator->get('test'));
    }

    /**
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
     * @expectedException \Interop\Container\Exception\NotFoundException
     * @expectedExceptionMessage Container: `test`, is not defined
     */
    public function testGetNameNotExist()
    {
        $locator = new Locator();
        $locator->get('test');
    }

    public function testSetLoader()
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
        new Locator([1]);
    }

    /**
     * @dataProvider providerValidParams
     * @param string $name
     * @param mixed $value
     */
    public function testGetUseLoader($name, $value)
    {

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
        $locator = new Locator([$loader]);
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

    public function testGetDependencyMap()
    {
        $locator = new Locator();
        $result = [];
        assertSame($result, $locator->getDependencyMap());
        $locator->set('test', 'magic');
        assertSame($result, $locator->getDependencyMap());
        $locator->get('test');
        $result += ['test' => []];
        assertSame($result, $locator->getDependencyMap());
    }
}
 