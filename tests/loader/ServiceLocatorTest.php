<?php
namespace smpl\mydi\loader;

use smpl\mydi\container\Factory;
use smpl\mydi\container\Service;
use smpl\mydi\LocatorInterface;

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase implements LocatorAwareInterface
{

    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    public static function mydiLoad(LocatorInterface $locator)
    {
        $callback = function (LocatorInterface $locator) {
            $obj = new \stdClass();
            $obj->magic = $locator->resolve('magic');
            return $obj;
        };

        return new Service($callback);
    }

    public function testIsLoadable()
    {
        $this->assertTrue($this->serviceLocator->isLoadable('\smpl\mydi\loader\ServiceLocatorTest'));
    }

    public function testIsLoadableInvalidClass()
    {
        $this->assertFalse($this->serviceLocator->isLoadable('\PHPUnit_Framework_TestCase'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container name must be a string
     */
    public function testIsLoadableNotString()
    {
        $this->serviceLocator->isLoadable(123);
    }

    public function testLoad()
    {
        $result = $this->serviceLocator->load('\smpl\mydi\loader\ServiceLocatorTest');
        $this->assertInstanceOf(Factory::class, $result);
        $locator = $this->getMock('\smpl\mydi\LocatorInterface');
        $locator->expects($this->once())
            ->method('resolve')
            ->with('magic')
            ->will($this->returnValue('my value'));
        /** @var LocatorInterface $locator */
        $obj = $result->resolve($locator);
        $this->assertInstanceOf(\stdClass::class, $obj);
        $this->assertSame('my value', $obj->magic);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`\PHPUnit_Framework_TestCase`, must be loadable
     */
    public function testLoadNotLoadable()
    {
        $this->serviceLocator->load('\PHPUnit_Framework_TestCase');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->serviceLocator = new ServiceLocator();
    }

}
