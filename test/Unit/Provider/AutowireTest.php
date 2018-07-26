<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Provider\Autowire;

class AutowireTest extends TestCase
{
    public function testIsProvide()
    {
        $autowire = new Autowire();
        $this->assertTrue($autowire->hasProvide(\stdClass::class));
        $this->assertFalse($autowire->hasProvide('invalid name'));
        return $autowire;
    }

    /**
     * @depends testIsProvide
     * @param Autowire $autowire
     */
    public function testProvide(Autowire $autowire)
    {
        $result = $autowire->provide(\stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertNotSame($result, $autowire->provide(\stdClass::class));
    }

    /**
     * @depends testIsProvide
     * @param Autowire $autowire
     */
    public function testProvideInvalid(Autowire $autowire)
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $autowire->provide('invalid name');
    }

    /**
     * @depends testIsProvide
     */
    public function testProvideWithParamName(Autowire $autowire)
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('name')
            ->willReturn('magic');
        $autowire->setContainer($container);
        $result = $autowire->provide(get_class(new class('asd')
        {
            public $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        }));

        $this->assertTrue(is_object($result));
        $this->assertSame('magic', $result->name);
    }

    /**
     * @depends testIsProvide
     */
    public function testProvideWithParamType(Autowire $autowire)
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(\stdClass::class)
            ->willReturn(new \stdClass());
        $autowire->setContainer($container);
        $result = $autowire->provide(get_class(new class(new \stdClass())
        {
            public $name;

            public function __construct(\stdClass $name)
            {
                $this->name = $name;
            }
        }));
        $this->assertTrue(is_object($result));
        $this->assertInstanceOf(\stdClass::class, $result->name);
    }

    /**
     * @depends testIsProvide
     */
    public function testProvideNotInstanceble(Autowire $autowire)
    {
        $this->expectException(ContainerExceptionInterface::class);
        $class = ContainerInterface::class;
        $this->expectExceptionMessage("$class is not instantiable");
        $autowire->provide($class);
    }
}
