<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\LoaderInterface;
use stdClass;

class ServiceTest extends TestCase
{
    public function testMustBeLoaderInterface()
    {
        $service = new Service(function () {
            return 123;
        });
        $this->assertInstanceOf(LoaderInterface::class, $service);
    }

    public function testLoad()
    {
        $service = new Service(function () {
            return new stdClass();
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

    public function testFromClassName()
    {
        $className = get_class(new class(1)
        {
            public $magic;

            public function __construct($magic)
            {
                $this->magic = $magic;
            }
        });
        $service = Service::fromClassName($className, ['magic']);
        $value = 123;
        $map = [
            ['magic', $value]
        ];
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->will($this->returnValueMap($map));
        /** @var ContainerInterface $container */
        $result = $service->load($container);
        $this->assertSame($value, $result->magic);
    }
}
