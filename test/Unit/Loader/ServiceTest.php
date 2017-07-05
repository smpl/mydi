<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;

class ServiceTest extends TestCase
{
    public function testGet()
    {
        $service = new Service(function () {
            return new \stdClass();
        });
        /** @var ContainerInterface $locator */
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $result = $service->get($locator);
        $this->assertSame($result, $service->get($locator));

        $service = new Service(function (ContainerInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $service->get($locator));
    }
}
 