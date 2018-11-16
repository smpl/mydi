<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\Reflection;
use Smpl\Mydi\Provider\Autowire;

class AutowireTest extends TestCase
{
    public function testIsProvide()
    {
        $autowire = new Autowire();
        $this->assertTrue($autowire->hasProvide(\stdClass::class));
        $this->assertFalse($autowire->hasProvide('invalid name'));
        return $autowire;
    }

    /**
     * @depends testIsProvide
     * @param Autowire $autowire
     */
    public function testProvide(Autowire $autowire)
    {
        $this->assertInstanceOf(Reflection::class, $autowire->provide(\stdClass::class));
    }

}
