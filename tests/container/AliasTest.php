<?php
namespace smpl\mydi\test\container;

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
        assertSame(123, $alias->get($locator));
        assertSame(123, $alias->get($locator));
    }
}