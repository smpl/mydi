<?php

namespace smpl\mydi\test\Loader;

use smpl\mydi\container\KeyValueJson;

class KeyValueJsonTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        file_put_contents('test.json', json_encode(self::getLoadertInterfaceConfiguration()));
        file_put_contents('empty', '');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink('test.json');
        unlink('empty');
    }

    /**
     * @dataProvider providerDataLoadertInterface
     * @param $key
     * @param $value
     */
    public function testLoadertInterfaceGet($key, $value)
    {
        $loader = new KeyValueJson('test.json');
        assertSame($value, $loader->get($key));
    }

    public function providerDataLoadertInterface()
    {
        $result = [];
        foreach (self::getLoadertInterfaceConfiguration() as $key => $value) {
            $call = [];
            $call[] = $key;
            $call[] = $value;
            $result[] = $call;
        }
        return $result;
    }

    /**
     * @return array
     */
    protected static function getLoadertInterfaceConfiguration()
    {
        return [
            "int" => 15,
            "string" => "some string",
            "float" => 0.5,
            "null" => null,
            "arrayWithKeyInt" => ["test0", "test1"],
            "arrayWithKeyString" => [
                "key1" => "value1",
                "key2" => 15
            ]
        ];
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `dsfdsfsdfds`, is not defined
     */
    public function testLoadertInterfaceInvalidConfiguration()
    {
        $loader = new KeyValueJson('test.json');
        $loader->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `not declared Container`, is not defined
     */
    public function testLoadertInterfaceGetNotDeclared()
    {
        $loader = new KeyValueJson('test.json');
        $loader->get('not declared Container');
    }

    /**
     * @expectedException \smpl\mydi\ContainerException
     */
    public function testNotReadable()
    {
        $loader = new KeyValueJson('not found');
        $loader->get('test');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `test`, is not defined
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