<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\Provider\Autowire;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleAliasClass;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentAnnotation;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentBaseType;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentDefaultValue;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentName;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentType;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleCustomStd;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleFactoryClass;

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
        /** @var LoaderInterface $loader */
        $loader = $this->autowire->get(\stdClass::class);
        $this->assertInstanceOf(Service::class, $loader);
        $container = $this->createMock(ContainerInterface::class);
        /** @var ContainerInterface $container */
        $result = $loader->get($container);
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testGetNameArgs()
    {
        /** @var LoaderInterface $loader */
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
        /** @var LoaderInterface $loader */
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
        /** @var LoaderInterface $loader */
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
        /** @var LoaderInterface $loader */
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
        /** @var LoaderInterface $loader */
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

    public function testGetFactory()
    {
        /** @var LoaderInterface $loader */
        $loader = $this->autowire->get(ExampleFactoryClass::class);

        $container = $this->createMock(ContainerInterface::class);
        /** @var ContainerInterface $container */

        $this->assertEquals($loader->get($container), $loader->get($container));
        $this->assertNotSame($loader->get($container), $loader->get($container));
    }

    public function testGetAlias()
    {
        /** @var LoaderInterface $loader */
        $loader = $this->autowire->get(ExampleAliasClass::class);

        $container = $this->createMock(ContainerInterface::class);
        $value = new ExampleCustomStd();
        $container->method('get')
            ->willReturn($value);
        /** @var ContainerInterface $container */

        $this->assertInstanceOf(\stdClass::class, $loader->get($container));

    }

    protected function setUp()
    {
        $this->autowire = new Autowire();
    }

}
