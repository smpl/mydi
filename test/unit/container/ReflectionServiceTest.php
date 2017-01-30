<?php
namespace smpl\mydi\test\unit\container;

use smpl\mydi\container\ReflectionService;
use smpl\mydi\loader\ObjectService;
use smpl\mydi\test\example\ClassArgument;
use smpl\mydi\test\example\ClassEmpty;
use smpl\mydi\test\example\ClassProxy;
use smpl\mydi\test\example\ClassProxyInjected;
use smpl\mydi\test\example\ClassProxyInjectMagic;
use smpl\mydi\test\example\ClassServiceAnnotation;
use smpl\mydi\test\example\ClassStd;

class ReflectionServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $executor = new ReflectionService();
        $result = $executor->get(ClassServiceAnnotation::class);
        $this->assertSame(ObjectService::class, get_class($result));
        $this->assertSame(ClassServiceAnnotation::class, self::getPrivateProperty($result, 'class')->getName());
        $this->assertSame([], self::getPrivateProperty($result, 'constructArgumentNames'));

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
        $executor = new ReflectionService();
        $this->assertTrue($executor->has(ClassServiceAnnotation::class));
        $this->assertFalse($executor->has('Invalid name'));
    }

    public function testChangeAnnotation()
    {
        $executor = new ReflectionService('');
        $this->assertTrue($executor->has(ClassArgument::class));
        $this->assertTrue($executor->get(ClassArgument::class) instanceof ObjectService);
    }

    public function testChangeInjectAnnotation()
    {
        $executor = new ReflectionService('', 'magic');
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
        $executor = new ReflectionService('');
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
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testGetWithoutAnnotation()
    {
        $factory = new ReflectionService();
        $this->assertFalse($factory->has(ClassEmpty::class));
        $factory->get(ClassEmpty::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Annotation must be string
     */
    public function testAnnotationNotString()
    {
        new ReflectionService([123]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Annotation to constructor must be string
     */
    public function testNotValidConstructAnnotation()
    {
        new ReflectionService('test', 123);
    }
}