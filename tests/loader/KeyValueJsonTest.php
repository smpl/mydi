<?php

namespace smpl\mydi\test\Loader;

use smpl\mydi\loader\KeyValueJson;
use smpl\mydi\LoaderInterface;
use smpl\mydi\test\LoaderInterfaceTestTrait;

class KeyValueJsonTest extends \PHPUnit_Framework_TestCase
{
    use LoaderInterfaceTestTrait;

    /**
     * @return LoaderInterface
     */
    public function getLoaderInterfaceObject()
    {
        return new KeyValueJson('test.json');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        file_put_contents('test.json', json_encode(self::$exampleConfiguration));
        file_put_contents('empty', '');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink('test.json');
        unlink('empty');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotReadable()
    {
        $loader = new KeyValueJson('not found');
        $loader->get('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`test`, must be loadable
     */
    public function testEmptyFile()
    {
        $loader = new KeyValueJson('empty');
        $loader->get('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage FileName must be string
     */
    public function testFileNameNotString()
    {
        new KeyValueJson(null);
    }
}