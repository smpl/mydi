<?php
namespace smpl\mydi\tests\unit\loader;

use smpl\mydi\loader\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    private static $resourceDir;
    /**
     * @var File
     */
    private $loader;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$resourceDir = __DIR__
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
            . 'FileTest';
    }

    protected function setUp()
    {
        parent::setUp();
        $this->loader = new File(self::$resourceDir);
    }


    public function testIsLoadable()
    {
        $this->assertSame(true, $this->loader->isLoadable('test'));
        $this->assertSame(false, $this->loader->isLoadable('invalidName'));
        $this->assertSame(true, $this->loader->isLoadable('subDir_test'));

        // Попытаемся загрузить что то за пределами указанного каталога (не должно грузить)
        $this->assertSame(false, $this->loader->isLoadable('../test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testIsLoadableNotString()
    {
        $this->loader->isLoadable(1);
    }

    public function testLoad()
    {
        // Загрузка простого компонента
        $this->assertSame(15, $this->loader->load('test'));
        $this->assertSame(15, $this->loader->load('subDir_test'));

        // проверим работу контекста
        $this->loader->setContext(['a' => 5]);
        $this->assertSame(20, $this->loader->load('testContext'));
        $this->loader->setContext(['a' => 7]);
        $this->assertSame(22, $this->loader->load('testContext'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @exceptedExceptionMessage Container:`invalid` must be loadable
     */
    public function testLoadInvalidContainer()
    {
        $this->loader->load('invalid');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testLoadNotString()
    {
        $this->loader->load(1);
    }

    /**
     * @expectedException \LogicException
     * @exceptedExceptionMessage Output in file: `%s` must be empty
     */
    public function testLoadNotEmptyOutput()
    {
        $this->loader->load('testOutput');
    }

    /**
     * @test
     */
    public function getContext()
    {
        $this->assertSame([], $this->loader->getContext());
    }

}
