<?php

namespace smpl\mydi\example\IoC;

use smpl\mydi\container\IoC;
use smpl\mydi\Locator;

class IoCTest extends \PHPUnit_Framework_TestCase
{
    public function testContainer()
    {
        $loader = new IoC(__DIR__);
        $locator = new Locator([$loader]);
        assertSame(1, $locator['testContainer']);
        assertSame(2, $locator['testContainer']);
        assertSame(3, $locator['testContainer']);
    }
}