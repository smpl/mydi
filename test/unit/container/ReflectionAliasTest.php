<?php
namespace smpl\mydi\tests\unit\container;

use smpl\mydi\container\ReflectionAlias;
use smpl\mydi\loader\Alias;
use smpl\mydi\test\example\ClassAliasAnnotation;
use smpl\mydi\test\example\ClassEmpty;
use smpl\mydi\test\example\ClassFactoryAnnotation;

class ReflectionAliasTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Annotation must be string
     */
    public function testAnnotationNotString()
    {
        new ReflectionAlias([123]);
    }

    public function testHas()
    {
        $alias = new ReflectionAlias();
        $alias->has(ClassAliasAnnotation::class);
    }

    public function testGet()
    {
        $alias = new ReflectionAlias();
        $loader = $alias->get(ClassAliasAnnotation::class);
        $this->assertTrue($loader instanceof Alias);
        $this->assertSame(ClassEmpty::class, $loader->getName());
    }

    /**
     * @expectedException \Interop\Container\Exception\ContainerException
     * @expectedExceptionMessage Alias target is unknow
     */
    public function testGetWithoutTarget()
    {
        $alias = new ReflectionAlias();
        $alias->get(ClassFactoryAnnotation::class);
    }

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testGetInvalidClass()
    {
        $alias = new ReflectionAlias();
        $alias->get('invalid class name');
    }
}