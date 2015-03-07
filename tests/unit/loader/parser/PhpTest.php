<?php
namespace smpl\mydi\tests\unit\loader\parser;

use ReflectionClass;
use smpl\mydi\loader\parser\Php;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    protected static $shortClass;
    /**
     * @var Php
     */
    protected $parser = 'smpl\mydi\loader\parser\Php';
    protected $file = 'php.php';
    protected $filePathInvalidFormat = __FILE__;

    public function testParse()
    {
        $result = $this->parser->parse();
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
        $this->parser->setFileName('invalid name file');
        $this->parser->parse();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseWithOutput()
    {
        $this->parser->setFileName($this->getResource('phpWithOutput.php'));
        $this->parser->parse();
    }

    public function testParseWithContext()
    {
        $this->parser->setFileName($this->getResource('testContext.php'));

        $this->parser->setContext(['a' => 7]);
        $this->assertSame(15 + 7, $this->parser->parse());

        $this->parser->setContext(['a' => 8]);
        $this->assertSame(15 + 8, $this->parser->parse());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File name must be string
     */
    public function testSetFileNameNotString()
    {
        $this->parser->setFileName(123);
    }

    protected function setUp()
    {
        parent::setUp();
        $reflect = new ReflectionClass($this);
        $this->filePathInvalidFormat = $this->getResource('phpInvalid.php');
        self::$shortClass = $reflect->getShortName();
        $this->parser = new $this->parser($this->getResource($this->file));
    }

    protected function getResource($file)
    {
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
}
