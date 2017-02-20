<?php
namespace Smpl\Mydi\Test\Unit\Extension;

use Smpl\Mydi\Provider\DynamicFile;

class DynamicFileTest extends \PHPUnit_Framework_TestCase
{
    private $pathConfiguration = __DIR__ . '/../../Example/DynamicFileConfig';

    public function testHas()
    {
        $loader = new DynamicFile($this->pathConfiguration);
        $this->assertSame(true, $loader->has('test'));
        $this->assertSame(false, $loader->has('invalidName'));
        $this->assertSame(true, $loader->has('subDir_test'));

        // Попытаемся загрузить что то за пределами указанного каталога (не должно грузить)
        $this->assertSame(false, $loader->has('../test'));
    }

    public function testhasNotString()
    {
        $loader = new DynamicFile(__DIR__ . DIRECTORY_SEPARATOR);
        assertFalse($loader->has(1));
    }

    public function testGet()
    {
        $loader = new DynamicFile($this->pathConfiguration, ['a' => 5]);
        // Загрузка простого компонента
        $this->assertSame(15, $loader->get('test'));
        $this->assertSame(15, $loader->get('subDir_test'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testNotDeclared()
    {
        $loader = new DynamicFile($this->pathConfiguration);
        $loader->get('not declared Container');
    }

    /**
     * @expectedException \Psr\Container\ContainerExceptionInterface
     * @exceptedExceptionMessage Container name must be string
     */
    public function testLoadNotString()
    {
        $loader = new DynamicFile($this->pathConfiguration);
        $loader->get(1);
    }
}
