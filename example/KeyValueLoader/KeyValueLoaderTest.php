<?php

namespace smpl\mydi\example\KeyValueLoader;

use smpl\mydi\loader\KeyValueJson;
use smpl\mydi\loader\KeyValuePhp;
use smpl\mydi\Locator;

class KeyValueLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonConfiguration()
    {
        $loader = new KeyValueJson(__DIR__ . DIRECTORY_SEPARATOR . 'example.json');
        $locator = new Locator([$loader]);

        assertSame(1, $locator['int']);
        assertTrue(is_int($locator['int']));
        assertSame(0.5, $locator['double']);
        assertTrue(is_double($locator['double']));
        assertSame('my magic string', $locator['string']);
        assertTrue(is_string($locator['string']));
        assertSame(null, $locator['null']);
        assertTrue(is_null($locator['null']));
        assertSame(['test' => 'someValue'], $locator['subArray']);
        assertTrue(is_array($locator['subArray']));
    }

    public function testPhpConfiguration()
    {
        $loader = new KeyValuePhp(__DIR__ . DIRECTORY_SEPARATOR . 'example.php');
        $locator = new Locator([$loader]);

        assertSame(1, $locator['int']);
        assertTrue(is_int($locator['int']));
        assertSame(0.5, $locator['double']);
        assertTrue(is_double($locator['double']));
        assertSame('my magic string', $locator['string']);
        assertTrue(is_string($locator['string']));
        assertSame(null, $locator['null']);
        assertTrue(is_null($locator['null']));
        assertSame(['test' => 'someValue'], $locator['subArray']);
        assertTrue(is_array($locator['subArray']));
    }

}