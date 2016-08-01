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
     * @dataProvider providerDataLoadertInterface
     * @param $key
     * @param $value
     */
    public function testLoadertInterfaceGet($key, $value)
    {
        assertSame($value, $this->createLoaderInterfaceObject()->get($key));
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
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `dsfdsfsdfds`, is not defined
     */
    public function testLoadertInterfaceInvalidConfiguration()
    {
        $this->createLoaderInterfaceObject()->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @expectedExceptionMessage Container: `not declared Container`, is not defined
     */
    public function testLoadertInterfaceGetNotDeclared()
    {
        $this->createLoaderInterfaceObject()->get('not declared Container');
    }

    public function testLoadertInterfaceGetContainerNames()
    {
        assertSame(array_keys(self::getLoadertInterfaceConfiguration()),
            $this->createLoaderInterfaceObject()->getContainerNames());
    }
}
