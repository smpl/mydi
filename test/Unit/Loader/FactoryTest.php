<?php
namespace Smpl\Mydi\Test\Unit\Loader;

use Interop\Container\ContainerInterface;
use Smpl\Mydi\Loader\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $factory = new Factory(function () {
            return new \stdClass();
        });
        /** @var ContainerInterface $locator */
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $result = $factory->get($locator);
        $this->assertEquals($result, $factory->get($locator));
        $this->assertNotSame($result, $factory->get($locator));

        $factory = new Factory(function (ContainerInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $factory->get($locator));
    }
}
 