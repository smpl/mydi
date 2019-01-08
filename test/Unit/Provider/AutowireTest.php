<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\Provider\Autowire;
use stdClass;

class AutowireTest extends TestCase
{
    public function testIsProvide()
    {
        $autowire = new Autowire();
        $this->assertTrue($autowire->hasProvide(\stdClass::class));
        $this->assertFalse($autowire->hasProvide('invalid name'));
        return $autowire;
    }

    public function testProvide()
    {
        $autowire = new Autowire();
        $loader = $autowire->provide(\stdClass::class);
        $this->assertInstanceOf(LoaderInterface::class, $loader);
        /** @var ContainerInterface $container */
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->assertEquals(new stdClass(), $loader->load($container));
    }

    public function testProvideWithArgument()
    {
        $autowire = new Autowire();
        $class = get_class(new class (123, new stdClass())
        {
            public $value;
            public $obj;

            public function __construct(int $value, stdClass $obj)
            {
                $this->value = $value;
                $this->obj = $obj;
            }
        });
        $loader = $autowire->provide($class);
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function (string $name) {
                $arr = [
                    'value' => 345,
                    stdClass::class => new stdClass()
                ];
                return $arr[$name];
            });
        /** @var ContainerInterface $container */
        $result = $loader->load($container);
        $this->assertInstanceOf($class, $result);
        $this->assertSame(345, $result->value);
        $this->assertEquals(new stdClass(), $result->obj);
    }

    public function testProvideInvalidName()
    {
        $autowire = new Autowire();
        $this->expectException(NotFound::class);
        $autowire->provide('invalid class name');
    }

}
