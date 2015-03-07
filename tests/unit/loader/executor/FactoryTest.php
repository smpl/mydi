<?php
namespace smpl\mydi\tests\unit\loader\executor;

use smpl\mydi\loader\executor\Factory;
use smpl\mydi\LocatorInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $executor;

    public function testExecute()
    {
        $locator = $this->getMock(LocatorInterface::class);
        /** @var LocatorInterface $locator */
        $result = $this->executor->execute('stdClass', []);
        $this->assertInstanceOf(\smpl\mydi\container\Factory::class, $result);
        $this->assertInstanceOf(\stdClass::class, $result->resolve($locator));
    }

    public function testExecuteWithString()
    {
        $locator = $this->getMock(LocatorInterface::class);
        $locator->expects($this->once())
            ->method('resolve')
            ->with('stdClass')
            ->will($this->returnValue(1234));
        /** @var LocatorInterface $locator */

        $result = $this->executor->execute('ololo', \stdClass::class);
        $this->assertInstanceOf(\smpl\mydi\container\Factory::class, $result);
        $this->assertSame(1234, $result->resolve($locator));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Config must be string or array
     */
    public function testExecuteInvalidConfig()
    {
        $this->executor->execute('test', null);
    }

    public function testExecuteWithConstructs()
    {
        $locator = $this->getMock(LocatorInterface::class);
        $locator->expects($this->once())
            ->method('resolve')
            ->with('test')
            ->will($this->returnValue('2000-01-01'));
        /** @var LocatorInterface $locator */
        $result = $this->executor->execute('\DateTime', ['construct' => ['test']]);
        /** @var \DateTime $obj */
        $obj = $result->resolve($locator);
        if (!$obj instanceof \Closure) {
            $this->assertSame('2000-01-01', $obj->format('Y-m-d'));
        } else {
            /** @var \Closure $obj */
            $this->assertSame('2000-01-01', $obj()->format('Y-m-d'));
        }

    }

    protected function setUp()
    {
        parent::setUp();
        $this->executor = new Factory();
    }
}
