<?php

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\ObjectService;
use Smpl\Mydi\Provider\Autowiring;
use Smpl\Mydi\Test\Example\CustomPDO;
use Smpl\Mydi\Test\Example\UserCustomPDO;
use Smpl\Mydi\Test\Example\UserMysqlPDO;

class AutowiringTest extends TestCase
{
    public function testHas()
    {
        $provider = new Autowiring();
        $this->assertTrue($provider->has(CustomPDO::class));
        $this->assertFalse($provider->has('invalid name'));
    }

    public function testGet()
    {
        $provider = new Autowiring();
        $this->assertInstanceOf(ObjectService::class, $provider->get(CustomPDO::class));
        $this->assertInstanceOf(ObjectService::class, $provider->get(UserCustomPDO::class));
        $this->assertInstanceOf(ObjectService::class, $provider->get(UserMysqlPDO::class));
        // @todo необходимо проверить с какими аргументами будет ObjectService
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetInvalid()
    {
        $provider = new Autowiring();
        $provider->get('invalid name class');
    }
}
