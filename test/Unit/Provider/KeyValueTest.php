<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\KeyValue;

class KeyValueTest extends TestCase
{

    public function testGet()
    {
        $key = 'magic';
        $value = 'magicValue';
        $provider = new KeyValue([$key => $value]);
        $this->assertSame($value, $provider->get($key));
    }

    public function testHas()
    {
        $key = 'magic';
        $value = 'magicValue';
        $provider = new KeyValue([$key => $value, 'fail' => null]);
        $this->assertTrue($provider->has($key));
        $this->assertFalse($provider->has('adasdhuasdh'));
        $this->assertTrue($provider->has('fail'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetInvalidName()
    {
        $provider = new KeyValue([]);
        $provider->get('asdasd');
    }

    public function testFromJsonFile()
    {
        $provider = KeyValue::fromJsonFile(__DIR__ . '/KeyValueTest/test.json');
        $this->assertTrue($provider->has('int'));
        $this->assertTrue($provider->has('null'));
        $this->assertTrue($provider->has('string'));
        $this->assertTrue($provider->has('float'));
        $this->assertTrue($provider->has('arrayWithKeyInt'));
        $this->assertTrue($provider->has('arrayWithKeyString'));

        $this->assertSame(15, $provider->get('int'));
        $this->assertSame(null, $provider->get('null'));
        $this->assertSame('some string', $provider->get('string'));
        $this->assertSame(0.5, $provider->get('float'));
        $this->assertSame([
            "test0",
            "test1"
        ], $provider->get('arrayWithKeyInt'));
        $this->assertSame([
            "key1" => "value1",
            "key2" => 15
        ], $provider->get('arrayWithKeyString'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage fileName: `ad8a8sda0s` is not readable
     */
    public function testFromJsonInvalidFile()
    {
        KeyValue::fromJsonFile('ad8a8sda0s');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFromJsonEmpty()
    {
        KeyValue::fromJsonFile(__DIR__ . '/KeyValueTest/empty.txt');
    }

    public function testFromPhp()
    {
        $provider = KeyValue::fromPhpFile(__DIR__ . '/KeyValueTest/test.php');

        $this->assertTrue($provider->has('int'));
        $this->assertTrue($provider->has('null'));
        $this->assertTrue($provider->has('string'));
        $this->assertTrue($provider->has('float'));
        $this->assertTrue($provider->has('arrayWithKeyInt'));
        $this->assertTrue($provider->has('arrayWithKeyString'));

        $this->assertSame(15, $provider->get('int'));
        $this->assertSame(null, $provider->get('null'));
        $this->assertSame('some string', $provider->get('string'));
        $this->assertSame(0.5, $provider->get('float'));
        $this->assertSame([
            "test0",
            "test1"
        ], $provider->get('arrayWithKeyInt'));
        $this->assertSame([
            "key1" => "value1",
            "key2" => 15
        ], $provider->get('arrayWithKeyString'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage fileName: `ad8a8sda0s` is not readable
     */
    public function testFromPhpInvalidFileName()
    {
        KeyValue::fromPhpFile('ad8a8sda0s');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFromPhpEmpty()
    {
        KeyValue::fromPhpFile(__DIR__ . '/KeyValueTest/empty.php');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp  /fileName: `[\w\/]*KeyValueTest\/invalid\.php` return invalid result/
     */
    public function testFromPhpInvalidContent()
    {
        KeyValue::fromPhpFile(__DIR__ . '/KeyValueTest/invalid.php');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp  /fileName: `[\w\/]*KeyValueTest\/invalid\.json` return invalid result/
     */
    public function testFromJsonInvalidContent()
    {
        KeyValue::fromJsonFile(__DIR__ . '/KeyValueTest/invalid.json');
    }
}
