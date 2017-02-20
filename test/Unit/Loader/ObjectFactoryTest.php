<?php
namespace Smpl\Mydi\tests\unit\loader;

use Interop\Container\ContainerInterface;
use Smpl\Mydi\Loader\ObjectFactory;
use Smpl\Mydi\Test\Example\ClassArgument;
use Smpl\Mydi\Test\Example\ClassEmpty;

class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStd()
    {
        $loader = ObjectFactory::factory(ClassEmpty::class);
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        /** @var ContainerInterface $locator */
        $result = $loader->get($locator);
        $this->assertTrue($result instanceof ClassEmpty);
        $this->assertNotSame($result, $loader->get($locator));
    }

    public function testGetWithDependency()
    {
        $loader = ObjectFactory::factory(ClassArgument::class, ['Example']);
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $argumentValue = 123;
        $locator->method('get')
            ->willReturn($argumentValue);
        /** @var ContainerInterface $locator */
        /** @var ClassArgument $result */
        $result = $loader->get($locator);
        $this->assertTrue($result instanceof ClassArgument);
        $this->assertSame($argumentValue, $result->value);
        $this->assertNotSame($result, $loader->get($locator));
    }

    public function testGetClass()
    {
        $object = new ObjectFactory(new \ReflectionClass(ClassEmpty::class));
        $this->assertSame(ClassEmpty::class, self::getPrivateProperty($object, 'class')->getName());
    }

    private static function getPrivateProperty($obj, $propertyName)
    {
        $r = new \ReflectionClass($obj);
        $p = $r->getProperty($propertyName);
        $p->setAccessible(true);
        return $p->getValue($obj);
    }

    public function testGetConstructArgumentNames()
    {
        $object = new ObjectFactory(new \ReflectionClass(ClassEmpty::class), ['123']);
        $this->assertSame(['123'], self::getPrivateProperty($object, 'constructArgumentNames'));
    }

    public function testFactory()
    {
        $object = ObjectFactory::factory(ClassEmpty::class);
        $this->assertSame(ClassEmpty::class, self::getPrivateProperty($object, 'class')->getName());
        $object = new ObjectFactory(new \ReflectionClass(ClassEmpty::class), ['123']);
        $this->assertSame(['123'], self::getPrivateProperty($object, 'constructArgumentNames'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Constructor arguments must be array of string
     */
    public function testConstructArgumentNamesNotArrayOfString()
    {
        new ObjectFactory(new \ReflectionClass(ClassEmpty::class), [123]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage ClassName must be string
     */
    public function testFactoryNotString()
    {
        ObjectFactory::factory(123);
    }
}