<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Mydi\Provider\KeyValue;

class KeyValueTest extends TestCase
{

    /**
     * @throws \Smpl\Mydi\Exception\NotFoundInterface
     */
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
     * @throws \Smpl\Mydi\Exception\NotFoundInterface
     */
    public function testProvideInvalidName()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $provider = new KeyValue([]);
        $provider->provide('asdasd');
    }

    /**
     * @throws \Smpl\Mydi\Exception\NotFoundInterface
     */
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

    public function testFromJsonInvalidFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('fileName: `ad8a8sda0s` is not readable');
        KeyValue::fromJson('ad8a8sda0s');
    }

    public function testFromJsonEmpty()
    {
        $this->expectException(\RuntimeException::class);
        KeyValue::fromJson(__DIR__ . '/KeyValueTest/empty.txt');
    }

    /**
     * @throws \Smpl\Mydi\Exception\NotFoundInterface
     */
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

    public function testFromPhpInvalidFileName()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('fileName: `ad8a8sda0s` is not readable');
        KeyValue::fromPhp('ad8a8sda0s');
    }

    public function testFromPhpEmpty()
    {
        $this->expectException(\RuntimeException::class);
        KeyValue::fromPhp(__DIR__ . '/KeyValueTest/empty.php');
    }

    public function testFromPhpInvalidContent()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/fileName: `[\w\/]*KeyValueTest\/invalid\.php` return invalid result/');
        KeyValue::fromPhp(__DIR__ . '/KeyValueTest/invalid.php');
    }

    public function testFromJsonInvalidContent()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/fileName: `[\w\/]*KeyValueTest\/invalid\.php` return invalid result/');
        KeyValue::fromJson(__DIR__ . '/KeyValueTest/invalid.php');
    }
}
