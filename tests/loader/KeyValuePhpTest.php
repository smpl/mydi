<?php

namespace smpl\mydi\test\Loader;

use smpl\mydi\loader\KeyValuePhp;
use smpl\mydi\LoaderInterface;
use smpl\mydi\test\LoaderInterfaceTestTrait;

class KeyValuePhpTest extends \PHPUnit_Framework_TestCase
{
    use LoaderInterfaceTestTrait;

    /**
     * @return LoaderInterface
     */
    public function getLoaderInterfaceObject()
    {
        return new KeyValuePhp('t.php');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        file_put_contents('t.php', '<?php return ' . var_export(self::$exampleConfiguration, true) . ';');
        file_put_contents('withOutput', '123');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink('t.php');
        unlink('withOutput');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetWithOutput()
    {
        $loader = new KeyValuePhp('withOutput');
        $loader->get('test');
    }
}