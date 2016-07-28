<?php

namespace smpl\mydi\example\Loader;

use smpl\mydi\container\Service;
use smpl\mydi\loader\KeyValueJson;
use smpl\mydi\Locator;
use smpl\mydi\LocatorInterface;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testExampleLoader()
    {
        $loaders = [];
        $loaders[] = new KeyValueJson(__DIR__ . DIRECTORY_SEPARATOR . 'example.json');
        $locator = new Locator($loaders);
        $locator['example'] = new Service(function(LocatorInterface $l) {
            $result = new \stdClass();
            $result->address = $l['address'];
            $result->username = $l['username'];
            $result->password = $l['password'];
            $result->name = $l['name'];
            return $result;
        });

        /** @var \stdClass $example */
        $example = $locator['example'];
        assertSame($locator['address'], $example->address);
        assertSame($locator['username'], $example->username);
        assertSame($locator['password'], $example->password);
        assertSame($locator['name'], $example->name);

        // test param from example.json
        assertSame('127.0.0.1', $locator['address']);
        assertSame('root', $locator['username']);
        assertSame('my secret password', $locator['password']);
        assertSame('db name', $locator['name']);
    }
}