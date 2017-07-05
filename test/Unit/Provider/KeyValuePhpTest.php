<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Extension;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\KeyValuePhp;

class KeyValuePhpTest extends TestCase
{
    private $pathConfiguration = __DIR__ . '/../../Example/KeyValuePhpConfig/';

    /**
     * @dataProvider providerDatae
     * @param $key
     * @param $value
     */
    public function testeGet($key, $value)
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 't.php');
        $this->assertSame($value, $loader->get($key));
    }

    public function providerDatae()
    {
        return [
            ["int", 15],
            ["string", "some string"],
            ["float", 0.5],
            ["null", null],
            [
                "arrayWithKeyInt",
                ["test0", "test1"]
            ],
            [
                "arrayWithKeyString",
                [
                    "key1" => "value1",
                    "key2" => 15
                ]
            ]
        ];
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testeInvalidConfiguration()
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 't.php');
        $loader->get('dsfdsfsdfds');
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testeGetNotDeclared()
    {
        $loader = new KeyValuePhp($this->pathConfiguration . 't.php');
        $loader->get('not declared Container');
    }

    /**
     * @expectedException \Psr\Container\ContainerExceptionInterface
     */
    public function testGetWithOutput()
    {
        $this->setOutputCallback(function () {
        });
        $loader = new KeyValuePhp($this->pathConfiguration . 'withOutput');
        $loader->get('test');
    }
}