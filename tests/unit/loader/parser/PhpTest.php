<?php
namespace smpl\mydi\tests\unit\loader\parser;

use ReflectionClass;
use smpl\mydi\loader\parser\Php;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Php
     */
    protected $parser = 'smpl\mydi\loader\parser\Php';
    protected $file = 'php.php';
    protected $filePathInvalidFormat = __FILE__;
    protected static $shortClass;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new $this->parser;
        $reflect = new ReflectionClass($this);
        $this->filePathInvalidFormat = $this->getResource('phpInvalid.php');
        self::$shortClass = $reflect->getShortName();
    }

    protected function getResource($file) {
        return __DIR__
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . 'resource'
        . DIRECTORY_SEPARATOR
        . 'unit'
        . DIRECTORY_SEPARATOR
        . 'loader'
        . DIRECTORY_SEPARATOR
        . 'parser'
        . DIRECTORY_SEPARATOR
        . self::$shortClass
        . DIRECTORY_SEPARATOR
        . $file;
    }

    public function testParse()
    {
        $result = $this->parser->parse($this->getResource($this->file));
        $valid = [
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
        $this->assertSame($valid, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage FileName: `invalid name file`, must be readable
     */
    public function testParseInvalidFileName()
    {
        $this->parser->parse('invalid name file');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseInvalidFormat()
    {
        $this->parser->parse($this->filePathInvalidFormat);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseWithOutput()
    {
        $this->parser->parse($this->getResource('phpWithOutput.php'));
    }
}
