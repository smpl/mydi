<?php
namespace smpl\mydi\test\unit\loader;

use smpl\mydi\loader\Factory;
use smpl\mydi\LocatorInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $factory = new Factory(function () {
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $result = $factory->get($locator);
        $this->assertEquals($result, $factory->get($locator));
        $this->assertNotSame($result, $factory->get($locator));

        $factory = new Factory(function (LocatorInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $factory->get($locator));
    }
}
 