<?php
namespace smpl\mydi;

class LocatorTest extends AbstractLoaderTest
{
    /**
     * @param $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testParams($name, $value)
    {
        $this->locator->add($name, $value);
        $this->assertSame($value, $this->locator->resolve($name));
        $this->locator->delete($name);
        $this->assertSame(false, $this->locator->isExist($name));
    }

    /**
     * Нет возможности создать два контейнера с одинаковым именем
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameExist()
    {
        $this->locator->add('test', 1);
        $this->locator->add('test', 1);
    }

    /**
     * В качестве ключа может быть строка это определено в интерфейсе смотри LocatorInterface и @see https://github.com/smpl/mydi/issues/3
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameNotString()
    {
        $this->locator->add(1, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteNotExist()
    {
        $this->locator->delete('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testResolveNameNotExist()
    {
        $this->locator->resolve('test');
    }

    public function testAddContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator->add('test', $mock);
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->locator->delete('test');
        $this->assertSame(false, $this->locator->isExist('test'));

        $this->locator->add('test', function () {
            return new \stdClass();
        });
        $result = $this->locator->resolve('test');
        $this->assertSame($result, $this->locator->resolve('test'));
        $this->assertTrue($result instanceof \stdClass);
    }

    /**
     * Бесконечное разрешение зависимостей #10 @see https://github.com/smpl/mydi/issues/10
     * Классный способ создать багию используя магические методы @see https://github.com/smpl/mydi/issues/13
     * @expectedException \InvalidArgumentException
     */
    public function testNotCorrectConfiguration()
    {
        $locator = $this->locator;
        $locator->add('a', function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->resolve('b');
            return $obj;
        });
        $locator->add('a', function () use ($locator) {
            $obj = new \stdClass();
            $obj->test = $locator->resolve('a');
            return $obj;
        });
        $locator->resolve('a');    // InvalidArgumentException
    }
}
 