<?php
namespace SmplTest\Mydi\tests\unit\loader;

use Smpl\Mydi\Loader\File\Readerinterface;
use Smpl\Mydi\Loader\KeyValue;

class KeyValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KeyValue
     */
    private $loader;
    /**
     * @var array
     */
    private $parsedConfig = [
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
    public function testLoad($key, $value)
    {
        $this->assertSame($value, $this->loader->load($key));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`test`, must be loadable
     */
    public function testInvalidConfiguration()
    {
        $this->loader->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`not declared Container`, must be loadable
     */
    public function testLoadNotDeclared()
    {
        $this->loader->load('not declared Container');
    }

    public function providerData()
    {
        $result = [];
        foreach ($this->parsedConfig as $key => $value) {
            $call = [];
            $call[] = $key;
            $call[] = $value;
            $result[] = $call;
        }
        return $result;
    }

    protected function setUp()
    {
        parent::setUp();

        $mock = $this->getMock(ReaderInterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($this->parsedConfig));
        /** @var Readerinterface $mock */
        $this->loader = new KeyValue($mock);
    }
}
