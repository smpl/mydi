<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;

class ServiceTest extends TestCase
{
    public function testLoad()
    {
        $service = new Service(function () {
            return new \stdClass();
        });
        /** @var ContainerInterface $locator */
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $result = $service->load($locator);
        $this->assertSame($result, $service->load($locator));

        $service = new Service(function (ContainerInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $service->load($locator));
    }
}
