<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Factory;

class FactoryTest extends TestCase
{
    public function testLoad()
    {
        $factory = new Factory(function () {
            return new \stdClass();
        });
        /** @var ContainerInterface $locator */
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $result = $factory->load($locator);
        $this->assertEquals($result, $factory->load($locator));
        $this->assertNotSame($result, $factory->load($locator));

        $factory = new Factory(function (ContainerInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $factory->load($locator));
    }
}
