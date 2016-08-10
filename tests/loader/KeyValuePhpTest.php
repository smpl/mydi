<?php

namespace smpl\mydi\test\Loader;

use smpl\mydi\container\KeyValuePhp;

class KeyValuePhpTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        file_put_contents('t.php', '<?php return ' . var_export(self::getLoadertInterfaceConfiguration(), true) . ';');
        file_put_contents('withOutput', '123');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink('t.php');
        unlink('withOutput');
    }

    /**
     * @dataProvider providerDataLoadertInterface
     * @param $key
     * @param $value
     */
    public function testLoadertInterfaceGet($key, $value)
    {
        $loader = new KeyValuePhp('t.php');
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
        $loader = new KeyValuePhp('t.php');
        $loader->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `not declared Container`, is not defined
     */
    public function testLoadertInterfaceGetNotDeclared()
    {
        $loader = new KeyValuePhp('t.php');
        $loader->get('not declared Container');
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