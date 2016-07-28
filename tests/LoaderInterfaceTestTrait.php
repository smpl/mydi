<?php
namespace smpl\mydi\test;

use smpl\mydi\LoaderInterface;

trait LoaderInterfaceTestTrait
{
    /**
     * @return LoaderInterface
     */
    abstract protected function createLoaderInterfaceObject();

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
     * @dataProvider providerData
     * @param $key
     * @param $value
     */
    public function testGet($key, $value)
    {
        assertSame($value, $this->createLoaderInterfaceObject()->get($key));
    }

    public function providerData()
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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container: `dsfdsfsdfds`, is not defined
     */
    public function testInvalidConfiguration()
    {
        $this->createLoaderInterfaceObject()->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container: `not declared Container`, is not defined
     */
    public function testGetNotDeclared()
    {
        $this->createLoaderInterfaceObject()->get('not declared Container');
    }

    public function testGetContainerNames()
    {
        assertSame(array_keys(self::getLoadertInterfaceConfiguration()),
            $this->createLoaderInterfaceObject()->getContainerNames());
    }
}
