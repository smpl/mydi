<?php
namespace SmplTest\Mydi;

use Smpl\Mydi\LoaderInterface;

trait LoaderInterfaceTestTrait
{
    /**
     * @return LoaderInterface
     */
    abstract public function getLoaderInterfaceObject();

    /**
     * @var array
     */
    protected static $exampleConfiguration = [
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

    /**
     * @dataProvider providerData
     * @param $key
     * @param $value
     */
    public function testGet($key, $value)
    {
        assertSame($value, $this->getLoaderInterfaceObject()->get($key));
    }

    public function providerData()
    {
        $result = [];
        foreach (self::$exampleConfiguration as $key => $value) {
            $call = [];
            $call[] = $key;
            $call[] = $value;
            $result[] = $call;
        }
        return $result;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`test`, must be loadable
     */
    public function testInvalidConfiguration()
    {
        $this->getLoaderInterfaceObject()->get('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`not declared Container`, must be loadable
     */
    public function testGetNotDeclared()
    {
        $this->getLoaderInterfaceObject()->get('not declared Container');
    }

    public function testGetContainerNames()
    {
        assertSame(array_keys(self::$exampleConfiguration), $this->getLoaderInterfaceObject()->getContainerNames());
    }
}
