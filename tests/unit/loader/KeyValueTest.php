<?php
namespace smpl\mydi\tests\unit\tests\unit\loader;

use smpl\mydi\loader\KeyValue;
use smpl\mydi\loader\ParserInterface;
use smpl\mydi\LoaderInterface;

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

    public function testGetParser()
    {
        $parser = $this->loader->getParser();
        $this->assertInstanceOf(ParserInterface::class, $parser);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`not declared container`, must be loadable
     */
    public function testLoadNotDeclared()
    {
        $this->loader->load('not declared container');
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
        $parser = $this->getMock('smpl\mydi\loader\ParserInterface');
        $parser->expects($this->any())
            ->method('parse')
            ->with($this->equalTo('mockedFile'))
            ->will($this->returnValue($this->parsedConfig));
        /** @var ParserInterface $parser */
        $this->loader = new KeyValue('mockedFile', $parser);
    }
}
