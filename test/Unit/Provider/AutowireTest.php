<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentAnnotation;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentBaseType;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentDefaultValue;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentName;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentType;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleCustomStd;

class AutowireTest extends TestCase
{
    /** @var Autowire */
    private $autowire;

    public function testHas()
    {
        $this->assertTrue($this->autowire->has(\stdClass::class));
        $this->assertFalse($this->autowire->has('some invalid class name'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetInvalid()
    {
        $this->autowire->get('some invalid class name');
    }

    public function testGetArgs()
    {
        $this->assertInstanceOf(Service::class, $this->autowire->get(\stdClass::class));
    }

    public function testGetNameArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentName::class);

        $container = $this->createMock(ContainerInterface::class);
        $value = 'magic';
        $container->method('get')
            ->with($this->equalTo('a'))
            ->willReturn($value);
        /** @var ContainerInterface $container */

        /** @var ExampleArgumentName $result */
        $result = $loader->get($container);
        $this->assertInstanceOf(ExampleArgumentName::class, $result);
        $this->assertSame($value, $result->a);
    }

    public function testGetTypeArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentType::class);

        $container = $this->createMock(ContainerInterface::class);
        $value = new \stdClass();
        $container->method('get')
            ->with($this->equalTo(\stdClass::class))
            ->willReturn($value);
        /** @var ContainerInterface $container */

        /** @var ExampleArgumentType $result */
        $result = $loader->get($container);
        $this->assertInstanceOf(\stdClass::class, $result->name);
    }

    public function testGetAnnotationArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentAnnotation::class);


        $container = $this->createMock(ContainerInterface::class);
        $value = new ExampleCustomStd();
        $container->method('get')
            ->with($this->equalTo(ExampleCustomStd::class))
            ->willReturn($value);
        /** @var ContainerInterface $container */

        /** @var ExampleArgumentAnnotation $result */
        $result = $loader->get($container);
        $this->assertInstanceOf(ExampleCustomStd::class, $result->class);

    }

    public function testGetNameWithDefaultArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentDefaultValue::class);

        $container = $this->createMock(ContainerInterface::class);
        $value = 'magic';
        $container->method('get')
            ->with($this->equalTo('a'))
            ->willReturn($value);
        /** @var ContainerInterface $container */

        /** @var ExampleArgumentName $result */
        $result = $loader->get($container);
        $this->assertInstanceOf(ExampleArgumentDefaultValue::class, $result);
        $this->assertSame($value, $result->a);
    }

    public function testGetBaseTypeArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentBaseType::class);

        $container = $this->createMock(ContainerInterface::class);
        $value = 'magic';
        $container->method('get')
            ->with($this->equalTo('a'))
            ->willReturn($value);
        /** @var ContainerInterface $container */

        /** @var ExampleArgumentBaseType $result */
        $result = $loader->get($container);
        $this->assertSame($value, $result->a);
    }

    protected function setUp()
    {
        $this->autowire = new Autowire();
    }

}
