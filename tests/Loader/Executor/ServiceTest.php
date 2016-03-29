<?php
namespace SmplTest\Mydi\Loader\Executor;

use Smpl\Mydi\Loader\Executor\Service;
use Smpl\Mydi\LocatorInterface;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    protected $executor;
    protected $wrapperResult = \stdClass::class;

    public function testExecute()
    {
        $locator = $this->getMock(LocatorInterface::class);
        /** @var LocatorInterface $locator */
        $result = $this->executor->execute('stdClass', []);
        $this->assertInstanceOf(\Smpl\Mydi\Container\Service::class, $result);
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
        $this->assertInstanceOf(\Smpl\Mydi\Container\Service::class, $result);
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
        $this->executor = new Service();
    }
}
