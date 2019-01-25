<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire\Reader;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Provider\Autowire\Reader\WithoutCache;
use stdClass;

class ReflectionTest extends TestCase
{

    public function testGetDependecies()
    {
        $reader = new WithoutCache();
        $this->assertSame([], $reader->getDependecies(stdClass::class));
    }

    public function testGetDependeciesInvalidName()
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $reader = new WithoutCache();
        $reader->getDependecies('invalid name');
    }
}
