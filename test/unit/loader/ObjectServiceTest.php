<?php
namespace smpl\mydi\tests\unit\loader;

use smpl\mydi\loader\ObjectService;
use smpl\mydi\LocatorInterface;
use smpl\mydi\test\example\ClassArgument;
use smpl\mydi\test\example\ClassEmpty;

class ObjectServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStd()
    {
        $loader = ObjectService::factory(ClassEmpty::class);
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        /** @var LocatorInterface $locator */
        $result = $loader->get($locator);
        $this->assertTrue($result instanceof ClassEmpty);
        $this->assertTrue($result === $loader->get($locator));
    }

    public function testGetWithDependency()
    {
        $loader = ObjectService::factory(ClassArgument::class, ['example']);
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $argumentValue = 123;
        $locator->method('get')
            ->willReturn($argumentValue);
        /** @var LocatorInterface $locator */
        /** @var ClassArgument $result */
        $result = $loader->get($locator);
        $this->assertTrue($result instanceof ClassArgument);
        $this->assertSame($argumentValue, $result->value);
        $this->assertTrue($result === $loader->get($locator));
    }

    public function testGetClass()
    {
        $object = new ObjectService(new \ReflectionClass(ClassEmpty::class));
        $this->assertSame(ClassEmpty::class, $object->getClass()->getName());
    }

    public function testGetConstructArgumentNames()
    {
        $object = new ObjectService(new \ReflectionClass(ClassEmpty::class), ['123']);
        $this->assertSame(['123'], $object->getConstructArgumentNames());
    }

    public function testFactory()
    {
        $object = ObjectService::factory(ClassEmpty::class);
        $this->assertSame(ClassEmpty::class, $object->getClass()->getName());
        $object = new ObjectService(new \ReflectionClass(ClassEmpty::class), ['123']);
        $this->assertSame(['123'], $object->getConstructArgumentNames());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Constructor arguments must be array of string
     */
    public function testConstructArgumentNamesNotArrayOfString()
    {
        new ObjectService(new \ReflectionClass(ClassEmpty::class), [123]);
    }


    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage ClassName must be string
     */
    public function testFactoryNotString()
    {
        ObjectService::factory(123);
    }
}