<?php
namespace SmplTest\Mydi\Container;

use Smpl\Mydi\Container\Factory;
use Smpl\Mydi\LocatorInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $factory = new Factory(function () {
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->createMock(LocatorInterface::class);
        $result = $factory->resolve($locator);
        $this->assertEquals($result, $factory->resolve($locator));
        $this->assertNotSame($result, $factory->resolve($locator));

        $factory = new Factory(function (LocatorInterface $locator) {
            return $locator;
        });
        $this->assertSame($locator, $factory->resolve($locator));
    }
}
 