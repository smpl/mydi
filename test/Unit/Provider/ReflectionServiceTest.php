<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Extension;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\ObjectService;
use Smpl\Mydi\Provider\ReflectionService;
use Smpl\Mydi\Test\Example\ClassArgument;
use Smpl\Mydi\Test\Example\ClassEmpty;
use Smpl\Mydi\Test\Example\ClassProxy;
use Smpl\Mydi\Test\Example\ClassProxyInjected;
use Smpl\Mydi\Test\Example\ClassProxyInjectMagic;
use Smpl\Mydi\Test\Example\ClassServiceAnnotation;
use Smpl\Mydi\Test\Example\ClassStd;

class ReflectionServiceTest extends TestCase
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
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetWithoutAnnotation()
    {
        $factory = new ReflectionService();
        $this->assertFalse($factory->has(ClassEmpty::class));
        $factory->get(ClassEmpty::class);
    }
}