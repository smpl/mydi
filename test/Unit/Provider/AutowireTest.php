<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Exception\NotFoundInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire;
use stdClass;

class AutowireTest extends TestCase
{

    /**
     * @var Autowire
     */
    private $autowire;

    public function setUp()
    {
        $this->autowire = new Autowire();
    }

    public function testHasProvide()
    {
        $this->assertTrue($this->autowire->hasProvide(stdClass::class));
        $this->assertFalse($this->autowire->hasProvide('invalid name'));
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvide()
    {
        $result = $this->autowire->provide(stdClass::class);
        $this->assertInstanceOf(Service::class, $result);
    }

    /**
     * @throws NotFoundInterface
     */
    public function testProvideNotFound()
    {
        $this->expectException(NotFoundInterface::class);
        $this->autowire->provide('invalid name');
    }
}
