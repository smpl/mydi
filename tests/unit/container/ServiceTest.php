<?php
namespace smpl\mydi\tests\unit\container;

use smpl\mydi\container\Service;
use smpl\mydi\LocatorInterface;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $service = new Service(function () {
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $result = $service->resolve($locator);
        $this->assertSame($result, $service->resolve($locator));

        $service = new Service(function (LocatorInterface $locator){
            return $locator;
        });
        $this->assertSame($locator, $service->resolve($locator));
    }
}
 