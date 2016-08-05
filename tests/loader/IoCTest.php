<?php
namespace smpl\mydi\test\Loader;

use Interop\Container\ContainerInterface;
use smpl\mydi\container\IoC;

class IoCTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $subDirTest = <<<'php'
<?php
return 15;
php;
        $test = <<<'php'
<?php
return 15;
php;
        $testOutput = <<<'php'
<?php
echo 'Magic';
return 15;
php;
        $root = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        mkdir($root);
        file_put_contents($root . 'test.php', $test);
        file_put_contents($root . 'testOutput.php', $testOutput);
        mkdir($root . 'subDir');
        file_put_contents(
            $root . 'subDir' . DIRECTORY_SEPARATOR . 'test.php',
            $subDirTest
        );
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $root = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        unlink($root . 'test.php');
        unlink($root . 'subDir' . DIRECTORY_SEPARATOR . 'test.php');
        unlink($root . 'testOutput.php');

        rmdir($root . 'subDir');
        rmdir($root);
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

    /**
     * @return ContainerInterface
     */
    protected function createLoaderInterfaceObject()
    {
        return new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
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

    protected static function getLoadertInterfaceConfiguration()
    {
        return [
            'subDir_test' => 15,
            'test' => 15,
        ];
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

    public function testHas()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
        $this->assertSame(true, $loader->has('test'));
        $this->assertSame(false, $loader->has('invalidName'));
        $this->assertSame(true, $loader->has('subDir_test'));

        // Попытаемся загрузить что то за пределами указанного каталога (не должно грузить)
        $this->assertSame(false, $loader->has('../test'));
    }

    public function testhasNotString()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
        assertFalse($loader->has(1));
    }

    public function testGet()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp', ['a' => 5]);
        // Загрузка простого компонента
        $this->assertSame(15, $loader->get('test'));
        $this->assertSame(15, $loader->get('subDir_test'));
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @exceptedExceptionMessage Container:`invalid` must be loadable
     */
    public function testLoadInvalidContainer()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
        $loader->get('invalid');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testLoadNotString()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
        $loader->get(1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLoadWithOutput()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
        $loader->get('testOutput');
    }
}
