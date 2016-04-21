<?php

namespace SmplExample\Mydi\Locator;

use Smpl\Mydi\Locator;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $locator = new Locator();
        $locator['c'] = 'mysql:dbname=example;host=localhost';
        $locator['b'] = new B($locator['c']);
        $locator['a'] = new A($locator['b']);

        assertSame('mysql:dbname=example;host=localhost', $locator['c']);
        assertTrue($locator['b'] instanceof B);
        assertTrue($locator['a'] instanceof A);
    }

}