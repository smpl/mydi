<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\Autowire\Reflection;
use stdClass;

class ReflectionTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testWithoutConstructor()
    {
        $reflection = new Reflection(new class
        {
        });
        $this->assertEmpty($reflection->getDependencies());
    }

    /**
     * @throws \ReflectionException
     */
    public function testWithContructor()
    {
        $reflection = new Reflection(new class (1, new stdClass())
        {
            public function __construct(int $primitive, stdClass $class)
            {

            }
        });
        $this->assertSame([
            'primitive' => 'primitive',
            'class' => stdClass::class
        ], $reflection->getDependencies());
    }
}
