<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Extension;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\DynamicFile;

class DynamicFileTest extends TestCase
{
    private $pathConfiguration = __DIR__ . '/DynamicFileConfigTest';

    public function testIsProvide()
    {
        $loader = new DynamicFile($this->pathConfiguration);
        $this->assertSame(true, $loader->hasProvide('test'));
        $this->assertSame(false, $loader->hasProvide('invalidName'));
        $this->assertSame(true, $loader->hasProvide('subDir_test'));

        $this->assertSame(false, $loader->hasProvide('../test'));
    }

    public function testProvide()
    {
        $loader = new DynamicFile($this->pathConfiguration);
        $this->assertSame(15, $loader->provide('test'));
        $this->assertSame(15, $loader->provide('subDir_test'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testProvideNotDeclared()
    {
        $loader = new DynamicFile($this->pathConfiguration);
        $loader->provide('not declared Container');
    }
}
