<?php

namespace SmplTest\Mydi\Loader;

use Smpl\Mydi\Loader\KeyValueJson;

class KeyValueJsonTest extends \PHPUnit_Framework_TestCase
{
    use LoaderInterfaceTestTrait;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        file_put_contents('test.json', json_encode(self::$exampleConfiguration));
        file_put_contents('empty', '');
    }


    protected function setUp()
    {
        parent::setUp();
        $this->loader = new KeyValueJson('test.json');
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
        $loader->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`test`, must be loadable
     */
    public function testEmptyFile()
    {
        $loader = new KeyValueJson('empty');
        $loader->load('test');
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