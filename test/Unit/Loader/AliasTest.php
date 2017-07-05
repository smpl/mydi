<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Alias;

class AliasTest extends TestCase
{
    public function testGet()
    {
        $alias = new Alias('test');
        $locator = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $locator->method('get')
            ->willReturn(123);
        /** @var ContainerInterface $locator */
        assertSame(123, $alias->get($locator));
        assertSame(123, $alias->get($locator));
    }

    public function testGetName()
    {
        $alias = new Alias('magic');
        $this->assertSame('magic', $alias->getName());
    }
}