<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Loader\Reflection;

class ReflectionTest extends TestCase
{
    public function testLoadWithoutConstructor()
    {
        $reflection = new Reflection(\stdClass::class);
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        /** @var ContainerInterface $container */
        $this->assertInstanceOf(\stdClass::class, $reflection->load($container));
    }

    public function testLoadWithConstructor()
    {
        $name = get_class(new class(new \stdClass(), 123)
        {
            public $class;
            public $magic;

            public function __construct(\stdClass $class, $magic)
            {
                $this->class = $class;
                $this->magic = $magic;
            }
        });
        $reflection = new Reflection($name);
        $container = $this->createMock(ContainerInterface::class);
        $map = [
            [\stdClass::class, new \stdClass()],
            ['magic', 123]
        ];
        $container->method('get')
            ->will($this->returnValueMap($map));
        /** @var ContainerInterface $container */
        $result = $reflection->load($container);
        $this->assertInstanceOf($name, $result);
    }

    public function testLoadInvalid()
    {
        $reflection = new Reflection('invalide name');
        $this->expectException(NotFoundExceptionInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        /** @var ContainerInterface $container */
        $reflection->load($container);
    }
}
