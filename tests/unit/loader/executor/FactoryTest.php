<?php
namespace smpl\mydi\tests\unit\loader\executor;

use smpl\mydi\loader\executor\Factory;
use smpl\mydi\LocatorInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $executorClass = Factory::class;
    protected $wrapperClass = \smpl\mydi\container\Factory::class;
    protected $wrapperResult = \stdClass::class;
    /**
     * @var Factory
     */
    protected $executor;

    public function testExecute()
    {
        $locator = $this->getMock(LocatorInterface::class);
        /** @var LocatorInterface $locator */
        $result = $this->executor->execute('stdClass', []);
        $this->assertInstanceOf($this->wrapperClass, $result);
        $this->assertInstanceOf($this->wrapperResult, $result->resolve($locator));
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
        $this->executor = new $this->executorClass();
    }
}
