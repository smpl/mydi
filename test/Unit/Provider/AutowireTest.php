<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire;
use Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleArgumentAnnotation;
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

        $locator = $this->createMock(ContainerInterface::class);
        $value = 'magic';
        $locator->method('get')
            ->with($this->equalTo('a'))
            ->willReturn($value);
        /** @var ContainerInterface $locator */

        /** @var ExampleArgumentName $result */
        $result = $loader->get($locator);
        $this->assertInstanceOf(ExampleArgumentName::class, $result);
        $this->assertSame($value, $result->a);
    }

    public function testGetTypeArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentType::class);

        $locator = $this->createMock(ContainerInterface::class);
        $value = new \stdClass();
        $locator->method('get')
            ->with($this->equalTo(\stdClass::class))
            ->willReturn($value);
        /** @var ContainerInterface $locator */

        /** @var ExampleArgumentType $result */
        $result = $loader->get($locator);
        $this->assertInstanceOf(\stdClass::class, $result->name);
    }

    public function testGetAnnotationArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentAnnotation::class);


        $locator = $this->createMock(ContainerInterface::class);
        $value = new ExampleCustomStd();
        $locator->method('get')
            ->with($this->equalTo(ExampleCustomStd::class))
            ->willReturn($value);
        /** @var ContainerInterface $locator */

        /** @var ExampleArgumentAnnotation $result */
        $result = $loader->get($locator);
        $this->assertInstanceOf(ExampleCustomStd::class, $result->class);

    }

    public function testGetNameWithDefaultArgs()
    {
        /** @var Service $loader */
        $loader = $this->autowire->get(ExampleArgumentDefaultValue::class);

        $locator = $this->createMock(ContainerInterface::class);
        $value = 'magic';
        $locator->method('get')
            ->with($this->equalTo('a'))
            ->willReturn($value);
        /** @var ContainerInterface $locator */

        /** @var ExampleArgumentName $result */
        $result = $loader->get($locator);
        $this->assertInstanceOf(ExampleArgumentDefaultValue::class, $result);
        $this->assertSame($value, $result->a);
    }

    protected function setUp()
    {
        $this->autowire = new Autowire();
    }

}
