<?php
namespace smpl\mydi\container;

use smpl\mydi\LocatorInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $factory = new Factory(function (){
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $result = $factory->resolve($locator);
        $this->assertEquals($result, $factory->resolve($locator));
        $this->assertNotSame($result, $factory->resolve($locator));

        $factory = new Factory(function (LocatorInterface $locator){
            return $locator;
        });
        $this->assertSame($locator, $factory->resolve($locator));
    }
}
 