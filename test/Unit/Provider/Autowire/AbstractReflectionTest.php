<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\Autowire\AbstractReflection;
use stdClass;

class AbstractReflectionTest extends TestCase
{
    public function testReadDependencies()
    {
        $dependencies = AbstractReflection::readDependencies(get_class(new class
        {
        }));
        $this->assertEmpty($dependencies);

        $dependencies = AbstractReflection::readDependencies(get_class(new class (1, new stdClass())
        {
            public function __construct(int $primitive, stdClass $class)
            {

            }
        }));
        $this->assertSame([
            'primitive' => 'primitive',
            'class' => stdClass::class
        ], $dependencies);
    }
}
