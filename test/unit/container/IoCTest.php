<?php
namespace smpl\mydi\test\unit\container;

use smpl\mydi\container\IoC;

class IoCTest extends \PHPUnit_Framework_TestCase
{
    private $pathConfiguration = __DIR__ . '/../../example/IocConfig';

    public function testHas()
    {
        $loader = new IoC($this->pathConfiguration);
        $this->assertSame(true, $loader->has('test'));
        $this->assertSame(false, $loader->has('invalidName'));
        $this->assertSame(true, $loader->has('subDir_test'));

        // Попытаемся загрузить что то за пределами указанного каталога (не должно грузить)
        $this->assertSame(false, $loader->has('../test'));
    }

    public function testhasNotString()
    {
        $loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR);
        assertFalse($loader->has(1));
    }

    public function testGet()
    {
        $loader = new IoC($this->pathConfiguration, ['a' => 5]);
        // Загрузка простого компонента
        $this->assertSame(15, $loader->get('test'));
        $this->assertSame(15, $loader->get('subDir_test'));
    }

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testNotDeclared()
    {
        $loader = new IoC($this->pathConfiguration);
        $loader->get('not declared Container');
    }

    /**
     * @expectedException \Interop\Container\Exception\ContainerException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testLoadNotString()
    {
        $loader = new IoC($this->pathConfiguration);
        $loader->get(1);
    }

    /**
     * @expectedException \Interop\Container\Exception\ContainerException
     */
    public function testLoadWithOutput()
    {
        $loader = new IoC($this->pathConfiguration);
        $loader->get('testOutput');
    }
}
