<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\KeyValueTransform;

class KeyValueTransformTest extends TestCase
{
    /**
     * @throws \Smpl\Mydi\Exception\NotFoundInterface
     */
    public function testTransform()
    {
        $provider = new KeyValueTransform([
            'a' => function () {
                return 123;
            }
        ]);
        $result = $provider->provide('a');
        $this->assertInstanceOf(Service::class, $result);
        $this->assertSame($result, $provider->provide('a'));
    }

}
