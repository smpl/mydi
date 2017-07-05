<?php
declare(strict_types=1);

namespace Smpl\Mydi\tests\unit\container;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\Provider\ReflectionAlias;
use Smpl\Mydi\Test\Example\ClassAliasAnnotation;
use Smpl\Mydi\Test\Example\ClassAliasAnnotationWithoutTarget;
use Smpl\Mydi\Test\Example\ClassEmpty;

class ReflectionAliasTest extends TestCase
{
    public function testHas()
    {
        $alias = new ReflectionAlias();
        $this->assertTrue($alias->has(ClassAliasAnnotation::class));
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