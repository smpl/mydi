<?php
namespace smpl\mydi\test\unit\loader;

use smpl\mydi\loader\Service;
use smpl\mydi\LocatorInterface;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $service = new Service(function () {
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $result = $service->get($locator);
        $this->assertSame($result, $service->get($locator));

        $service = new Service(function (LocatorInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $service->get($locator));
    }
}
 