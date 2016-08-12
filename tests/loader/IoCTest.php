<?php
namespace smpl\mydi\test\loader;

use Interop\Container\ContainerInterface;
use smpl\mydi\container\IoC;

class IoCTest extends \PHPUnit_Framework_TestCase
{
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
        return new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample');
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
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample');
        $this->assertSame(true, $loader->has('test'));
        $this->assertSame(false, $loader->has('invalidName'));
        $this->assertSame(true, $loader->has('subDir_test'));

        // Попытаемся загрузить что то за пределами указанного каталога (не должно грузить)
        $this->assertSame(false, $loader->has('../test'));
    }

    public function testhasNotString()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample');
        assertFalse($loader->has(1));
    }

    public function testGet()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample', ['a' => 5]);
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
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample');
        $loader->get('invalid');
    }

    /**
     * @expectedException \smpl\mydi\NotFoundException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testLoadNotString()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample');
        $loader->get(1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLoadWithOutput()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'IocTestExample');
        $loader->get('testOutput');
    }
}
