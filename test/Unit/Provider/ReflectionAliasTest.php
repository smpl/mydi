<?php
namespace Smpl\Mydi\tests\unit\container;

use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\Provider\ReflectionAlias;
use Smpl\Mydi\Test\Example\ClassAliasAnnotation;
use Smpl\Mydi\Test\Example\ClassAliasAnnotationWithoutTarget;
use Smpl\Mydi\Test\Example\ClassEmpty;

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
     * @expectedException \Psr\Container\ContainerExceptionInterface
     * @expectedExceptionMessage Alias target is unknow
     */
    public function testGetWithoutTarget()
    {
        $alias = new ReflectionAlias();
        $alias->get(ClassAliasAnnotationWithoutTarget::class);
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetInvalidClass()
    {
        $alias = new ReflectionAlias();
        $alias->get(ClassEmpty::class);
    }
}