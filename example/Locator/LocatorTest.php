<?php

namespace SmplExample\Mydi\Locator;

use Smpl\Mydi\Loader\IoC;
use Smpl\Mydi\Loader\KeyValueJson;
use Smpl\Mydi\Locator;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testDependencyMap()
    {
        $json = new KeyValueJson(__DIR__ . DIRECTORY_SEPARATOR . 'example.json');
        $ioc = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'example');
        $loaders = [$json, $ioc];

        $locator = new Locator($loaders);

        assertSame([
            'db.dsn' => [],
            'db.user' => [],
            'db.password' => [],
            'db' => ['db.dsn', 'db.user', 'db.password'],
            'magic' => ['db'],
        ], $locator->getDependencyMap());
    }

    public function test()
    {

        $json = new KeyValueJson(__DIR__ . DIRECTORY_SEPARATOR . 'example.json');
        $ioc = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'example');
        $loaders = [$json, $ioc];

        $locator = new Locator($loaders);

        assertSame('mysql:dbname=example;host=localhost', $locator['db.dsn']); // see in example.json
        assertSame('root', $locator['db.user']); // see in example.json
        assertSame('secretPassword', $locator['db.password']); // see in example.json

        $magic = $locator['magic']; // see example/magic.php result function is here
        assertTrue($magic instanceof \stdClass);
        assertTrue($magic->db instanceof \stdClass);

        $db = $locator['db']; // see example/db.php result function is here
        assertTrue($db instanceof \stdClass);
        assertSame('mysql:dbname=example;host=localhost', $db->dsn);
        assertSame('root', $db->user);
        assertSame('secretPassword', $db->password);

        assertSame($magic->db, $db);
    }

}