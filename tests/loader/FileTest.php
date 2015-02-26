<?php
namespace smpl\mydi\loader;

class FileTest extends \PHPUnit_Framework_TestCase
{
    private static $dir = 'resource';
    /**
     * @var File
     */
    private $loader;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        mkdir(__DIR__ . DIRECTORY_SEPARATOR . self::$dir);
        $content = <<<'php'
<?php
return 15;
php;
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'test.php', $content);
        mkdir(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'subDir');
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'subDir/test.php', $content);
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'test.php', $content);
        $content = <<<'php'
<?php
return 15 + $a;
php;
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'testContext.php', $content);
        $content = <<<'php'
<?php
echo 'Magic';
return 15;
php;
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'testOutput.php', $content);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->loader = new File(__DIR__ . DIRECTORY_SEPARATOR . self::$dir);
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

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'subDir/test.php');
        rmdir(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'subDir');
        unlink(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'test.php');
        unlink(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'testContext.php');
        unlink(__DIR__ . DIRECTORY_SEPARATOR . self::$dir . DIRECTORY_SEPARATOR . 'testOutput.php');
        rmdir(__DIR__ . DIRECTORY_SEPARATOR . self::$dir);
    }

}
