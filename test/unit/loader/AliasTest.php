<?php
namespace smpl\mydi\test\unit\loader;

use smpl\mydi\loader\Alias;
use smpl\mydi\LocatorInterface;

class AliasTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $alias = new Alias('test');
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $locator->method('get')
            ->will($this->returnValue(123));
        /** @var LocatorInterface $locator */
        assertSame(123, $alias->get($locator));
        assertSame(123, $alias->get($locator));
    }
}