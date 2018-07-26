<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Alias;

class AliasTest extends TestCase
{
    public function testLoad()
    {
        $alias = new Alias('test');
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->method('get')
            ->willReturn(123);
        /** @var ContainerInterface $container */
        $this->assertSame(123, $alias->load($container));
        $this->assertSame(123, $alias->load($container));
    }
}
