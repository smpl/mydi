<?php
namespace smpl\mydi\test;

use smpl\mydi\LocatorInterface;

trait LocatorInterfaceTestTrait
{
    /**
     * @return LocatorInterface
     */
    abstract protected function createLocatorInterfaceObject();

    public function providerValidParams()
    {
        return [
            ['int', 1],
            ['float', 0.5],
            ['bool', true],
            ['string', 'test'],
            ['object', new \stdClass()],
            ['null', null]
        ];
    }

    /**
     * @param $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testArrayParams($name, $value)
    {
        $locator = $this->createLocatorInterfaceObject();
        $locator[$name] = $value;
        $this->assertSame($value, $locator[$name]);
        $this->assertSame(true, isset($locator[$name]));
        unset($locator[$name]);
        $this->assertSame(false, isset($locator[$name]));
    }


    public function testArraySetNameExist()
    {
        $locator['test'] = 1;
        $this->assertSame(1, $locator['test']);
        $locator['test'] = 2;
        $this->assertSame(2, $locator['test']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArraySetNameNotString()
    {
        $locator = $this->createLocatorInterfaceObject();
        $locator[1] = 1;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayDeleteNotExist()
    {
        $locator = $this->createLocatorInterfaceObject();
        unset($locator['test']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayGetNameNotExist()
    {
        $locator = $this->createLocatorInterfaceObject();
        $locator['test'];
    }
}