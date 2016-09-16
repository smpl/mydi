<?php
namespace smpl\mydi\test\unit\container;

use smpl\mydi\container\KeyValueJson;

class KeyValueJsonTest extends \PHPUnit_Framework_TestCase
{
    private $pathConfiguration = __DIR__ . DIRECTORY_SEPARATOR . 'KeyValueJsonExample' . DIRECTORY_SEPARATOR;

    /**
     * @dataProvider providerData
     * @param $key
     * @param $value
     */
    public function testGet($key, $value)
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'test.json');
        assertSame($value, $loader->get($key));
    }

    public function providerData()
    {

        return [
            ["int", 15],
            ["string", "some string"],
            ["float", 0.5],
            ["null", null],
            [
                "arrayWithKeyInt",
                ["test0", "test1"]
            ],
            [
                "arrayWithKeyString",
                [
                    "key1" => "value1",
                    "key2" => 15
                ]
            ]
        ];
    }

    public function testHas()
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'test.json');
        assertTrue($loader->has("int"));
        assertFalse($loader->has('invalid name'));
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `dsfdsfsdfds`, is not defined
     */
    public function testInvalidConfiguration()
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'test.json');
        $loader->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `not declared Container`, is not defined
     */
    public function testGetNotDeclared()
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'test.json');
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
        $loader = new KeyValueJson($this->pathConfiguration . 'empty.txt');
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