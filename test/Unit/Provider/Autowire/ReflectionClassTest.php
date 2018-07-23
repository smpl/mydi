<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Provider\Autowire\ReflectionClass;

class ReflectionClassTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testCreateClosure()
    {
        $class = new ReflectionClass(get_class(new class
        {
        }));
        $closure = $class->createClosure();
        $this->assertInstanceOf(\Closure::class, $closure);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->never())->method('get');
        $this->assertTrue(is_object(call_user_func_array($closure, [$container])));
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateClosureWithParamName()
    {
        $class = new ReflectionClass(get_class(new class('asd')
        {
            public $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
        }));
        $closure = $class->createClosure();
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('name')
            ->willReturn('magic');
        $result = call_user_func_array($closure, [$container]);
        $this->assertTrue(is_object($result));
        $this->assertSame('magic', $result->name);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateClosureWithParamType()
    {
        $class = new ReflectionClass(get_class(new class(new \stdClass())
        {
            public $name;

            public function __construct(\stdClass $name)
            {
                $this->name = $name;
            }
        }));
        $closure = $class->createClosure();
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(\stdClass::class)
            ->willReturn(new \stdClass());
        $result = call_user_func_array($closure, [$container]);
        $this->assertTrue(is_object($result));
        $this->assertInstanceOf(\stdClass::class, $result->name);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateClosureNotInstanceble()
    {
        $this->expectException(ContainerExceptionInterface::class);
        $class = ContainerInterface::class;
        $this->expectExceptionMessage("$class is not instantiable");
        $result = new ReflectionClass($class);
        $result->createClosure();
    }
}
