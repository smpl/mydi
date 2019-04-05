<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Smpl\Mydi\Provider\Autowire\ReflectionClass;
use stdClass;

class ReflectionClassTest extends TestCase
{

    /**
     * @throws ReflectionException
     */
    public function testGetConstructDependenciesWithoutConstruct()
    {
        $obj = new class
        {
        };
        $class = new ReflectionClass($obj);
        $this->assertEmpty($class->getConstructDependencies());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetConstructDependenciesWithConstructParams()
    {
        $obj = new class(new stdClass())
        {
            public function __construct(stdClass $bar, int $foo = 123)
            {
            }
        };
        $class = new ReflectionClass($obj);
        $this->assertSame(['bar' => 'stdClass', 'foo' => 'foo'], $class->getConstructDependencies());
    }
}
