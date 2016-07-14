<?php

namespace SmplExample\IoC;

use Smpl\Mydi\Loader\IoC;
use Smpl\Mydi\Locator;

class IoCTest extends \PHPUnit_Framework_TestCase
{
    public function testContext()
    {
        $loader = new IoC(__DIR__, ['magic' => 3]);
        $locator = new Locator([$loader]);
        assertSame(13, $locator['testContext']);

        // change context to another value
        $loader = new IoC(__DIR__, ['magic' => 7]);
        $locator = new Locator([$loader]);
        assertSame(17, $locator['testContext']);
    }

    public function testContainer()
    {
        $loader = new IoC(__DIR__);
        $locator = new Locator([$loader]);
        assertSame(1, $locator['testContainer']);
        assertSame(2, $locator['testContainer']);
        assertSame(3, $locator['testContainer']);
    }
}