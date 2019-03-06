<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\DynamicFileTransform;

class DynamicFileTransformTest extends TestCase
{
    private $pathConfiguration = __DIR__ . '/DynamicFileConfigTest';

    /**
     * @throws \Smpl\Mydi\Exception\NotFoundInterface
     */
    public function testTransform()
    {
        $provider = new DynamicFileTransform($this->pathConfiguration);
        $result = $provider->provide('closure');
        $this->assertInstanceOf(Service::class, $result);
        $this->assertSame($result, $provider->provide('closure'));
    }
}
