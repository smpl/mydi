<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire;

class AutowireTest extends TestCase
{
    public function testHas()
    {
        $autowire = new Autowire();
        $this->assertTrue($autowire->has(\stdClass::class));
        $this->assertFalse($autowire->has('invalid name'));
        return $autowire;
    }

    /**
     * @depends testHas
     * @param Autowire $autowire
     */
    public function testGet(Autowire $autowire)
    {
        $result = $autowire->get(\stdClass::class);
        $this->assertInstanceOf(Service::class, $result);
        $this->assertNotSame($result, $autowire->get(\stdClass::class));
    }

    /**
     * @depends testHas
     * @param Autowire $autowire
     */
    public function testGetInvalid(Autowire $autowire)
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $autowire->get('invalid name');
    }
}
