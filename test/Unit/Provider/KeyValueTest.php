<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use Closure;
use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\KeyValue;

class KeyValueTest extends TestCase
{

    public function testProvide()
    {
        $key = 'magic';
        $value = 'magicValue';
        $provider = new KeyValue([$key => $value]);
        $this->assertSame($value, $provider->provide($key));
    }

    public function testIsProvide()
    {
        $key = 'magic';
        $value = 'magicValue';
        $provider = new KeyValue([$key => $value, 'fail' => null]);
        $this->assertTrue($provider->hasProvide($key));
        $this->assertFalse($provider->hasProvide('adasdhuasdh'));
        $this->assertTrue($provider->hasProvide('fail'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testProvideInvalidName()
    {
        $provider = new KeyValue([]);
        $provider->provide('asdasd');
    }

    public function testFromJsonFile()
    {
        $provider = KeyValue::fromJson(__DIR__ . '/KeyValueTest/test.json');
        $this->assertTrue($provider->hasProvide('int'));
        $this->assertTrue($provider->hasProvide('null'));
        $this->assertTrue($provider->hasProvide('string'));
        $this->assertTrue($provider->hasProvide('float'));
        $this->assertTrue($provider->hasProvide('arrayWithKeyInt'));
        $this->assertTrue($provider->hasProvide('arrayWithKeyString'));

        $this->assertSame(15, $provider->provide('int'));
        $this->assertSame(null, $provider->provide('null'));
        $this->assertSame('some string', $provider->provide('string'));
        $this->assertSame(0.5, $provider->provide('float'));
        $this->assertSame([
            "test0",
            "test1"
        ], $provider->provide('arrayWithKeyInt'));
        $this->assertSame([
            "key1" => "value1",
            "key2" => 15
        ], $provider->provide('arrayWithKeyString'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage fileName: `ad8a8sda0s` is not readable
     */
    public function testFromJsonInvalidFile()
    {
        KeyValue::fromJson('ad8a8sda0s');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFromJsonEmpty()
    {
        KeyValue::fromJson(__DIR__ . '/KeyValueTest/empty.txt');
    }

    public function testFromPhp()
    {
        $provider = KeyValue::fromPhp(__DIR__ . '/KeyValueTest/test.php');

        $this->assertTrue($provider->hasProvide('int'));
        $this->assertTrue($provider->hasProvide('null'));
        $this->assertTrue($provider->hasProvide('string'));
        $this->assertTrue($provider->hasProvide('float'));
        $this->assertTrue($provider->hasProvide('arrayWithKeyInt'));
        $this->assertTrue($provider->hasProvide('arrayWithKeyString'));

        $this->assertSame(15, $provider->provide('int'));
        $this->assertSame(null, $provider->provide('null'));
        $this->assertSame('some string', $provider->provide('string'));
        $this->assertSame(0.5, $provider->provide('float'));
        $this->assertSame([
            "test0",
            "test1"
        ], $provider->provide('arrayWithKeyInt'));
        $this->assertSame([
            "key1" => "value1",
            "key2" => 15
        ], $provider->provide('arrayWithKeyString'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage fileName: `ad8a8sda0s` is not readable
     */
    public function testFromPhpInvalidFileName()
    {
        KeyValue::fromPhp('ad8a8sda0s');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFromPhpEmpty()
    {
        KeyValue::fromPhp(__DIR__ . '/KeyValueTest/empty.php');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp  /fileName: `[\w\/]*KeyValueTest\/invalid\.php` return invalid result/
     */
    public function testFromPhpInvalidContent()
    {
        KeyValue::fromPhp(__DIR__ . '/KeyValueTest/invalid.php');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp  /fileName: `[\w\/]*KeyValueTest\/invalid\.php` return invalid result/
     */
    public function testFromJsonInvalidContent()
    {
        KeyValue::fromJson(__DIR__ . '/KeyValueTest/invalid.php');
    }

    public function testTransformToService()
    {
        $keyValue = new KeyValue([
            'magic' => function () {
                return 123;
            }
        ]);

        $this->assertInstanceOf(Closure::class, $keyValue->provide('magic'));
        $keyValue->transformClosureToService();
        $this->assertInstanceOf(Service::class, $keyValue->provide('magic'));
    }
}
