<?php
namespace Smpl\Mydi\Test\Unit\Extension;

use Smpl\Mydi\Loader\ObjectFactory;
use Smpl\Mydi\Provider\ReflectionFactory;
use Smpl\Mydi\Test\Example\ClassArgument;
use Smpl\Mydi\Test\Example\ClassEmpty;
use Smpl\Mydi\Test\Example\ClassFactoryAnnotation;
use Smpl\Mydi\Test\Example\ClassProxy;
use Smpl\Mydi\Test\Example\ClassProxyInjected;
use Smpl\Mydi\Test\Example\ClassProxyInjectMagic;
use Smpl\Mydi\Test\Example\ClassStd;

class ReflectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $executor = new ReflectionFactory();
        $obj = $executor->get(ClassFactoryAnnotation::class);
        $this->assertSame(ObjectFactory::class, get_class($obj));
        $this->assertSame(ClassFactoryAnnotation::class, self::getPrivateProperty($obj, 'class')->getName());
        $this->assertSame([], self::getPrivateProperty($obj, 'constructArgumentNames'));

    }

    private static function getPrivateProperty($obj, $propertyName)
    {
        $r = new \ReflectionClass($obj);
        $p = $r->getProperty($propertyName);
        $p->setAccessible(true);
        return $p->getValue($obj);
    }

    public function testHas()
    {
        $executor = new ReflectionFactory();
        $this->assertTrue($executor->has(ClassFactoryAnnotation::class));
        $this->assertFalse($executor->has('Invalid name'));
    }

    public function testChangeAnnotation()
    {
        $executor = new ReflectionFactory('');
        $this->assertTrue($executor->has(ClassArgument::class));
        $this->assertTrue($executor->get(ClassArgument::class) instanceof ObjectFactory);
    }

    public function testChangeInjectAnnotation()
    {
        $executor = new ReflectionFactory('', 'magic');
        $result = $executor->get(ClassProxyInjectMagic::class);
        $this->assertSame([ClassStd::class], self::getPrivateProperty($result, 'constructArgumentNames'));
    }

    /**
     * @param string $name
     * @param string $assert
     * @dataProvider dataProviderValid
     */
    public function testGetWithParameterByType($name, $assert)
    {
        $executor = new ReflectionFactory('');
        $result = $executor->get($name);
        $this->assertSame($assert, self::getPrivateProperty($result, 'constructArgumentNames'));
    }

    public function dataProviderValid()
    {
        return [
            [ClassProxy::class, [ClassStd::class]],
            [ClassProxyInjected::class, [ClassStd::class]],
            [ClassArgument::class, ['value']],
        ];
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetWithoutAnnotation()
    {
        $factory = new ReflectionFactory();
        $this->assertFalse($factory->has(ClassEmpty::class));
        $factory->get(ClassEmpty::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Annotation must be string
     */
    public function testAnnotationNotString()
    {
        new ReflectionFactory([123]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Annotation to constructor must be string
     */
    public function testNotValidConstructAnnotation()
    {
        new ReflectionFactory('test', 123);
    }
}