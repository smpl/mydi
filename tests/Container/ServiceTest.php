<?php
namespace SmplTest\Mydi\Container;

use Smpl\Mydi\Container\Service;
use Smpl\Mydi\LocatorInterface;

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
 