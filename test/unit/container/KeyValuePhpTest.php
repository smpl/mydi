<?php
namespace smpl\mydi\test\unit\container;

use smpl\mydi\container\KeyValuePhp;

class KeyValuePhpTest extends \PHPUnit_Framework_TestCase
{
    private $pathConfiguration = __DIR__ . '/../../example/KeyValuePhpConfig/';
    /**
     * @dataProvider providerDatae
     * @param $key
     * @param $value
     */
    public function testeGet($key, $value)
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 't.php');
        assertSame($value, $loader->get($key));
    }

    public function providerDatae()
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

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testeInvalidConfiguration()
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 't.php');
        $loader->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testeGetNotDeclared()
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 't.php');
        $loader->get('not declared Container');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetWithOutput()
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 'withOutput');
        $loader->get('test');
    }
}