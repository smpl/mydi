<?php
namespace SmplTest\Mydi\Container;

use Smpl\Mydi\Container\Service;
use Smpl\Mydi\LocatorInterface;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $service = new Service(function () {
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->createMock(LocatorInterface::class);
        $result = $service->resolve($locator);
        $this->assertSame($result, $service->resolve($locator));

        $service = new Service(function (LocatorInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $service->resolve($locator));
    }
}
 