<?php
namespace Smpl\Mydi\Test\Unit\Extension;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\KeyValueJson;

class KeyValueJsonTest extends TestCase
{
    private $pathConfiguration = __DIR__ . '/../../Example/KeyValueJsonConfig/';

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
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testInvalidConfiguration()
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'test.json');
        $loader->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetNotDeclared()
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'test.json');
        $loader->get('not declared Container');
    }

    /**
     * @expectedException \Psr\Container\ContainerExceptionInterface
     */
    public function testNotReadable()
    {
        $loader = new KeyValueJson('not found');
        $loader->get('test');
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testEmptyFile()
    {
        $loader = new KeyValueJson($this->pathConfiguration . 'empty.txt');
        $loader->get('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage FilePath must be string
     */
    public function testFileNameNotString()
    {
        new KeyValueJson(null);
    }
}