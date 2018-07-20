<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Factory;

class FactoryTest extends TestCase
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

    public function testFromReflectionClass()
    {
        $class = $this->createMock(\ReflectionClass::class);
        /** @var \ReflectionClass $class */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('test');
        /** @var ContainerInterface $container */

        $factory = Factory::fromReflectionClass($class, ['test']);
        $factory->get($container);
    }
}
