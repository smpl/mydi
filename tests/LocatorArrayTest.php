<?php
namespace SmplTest\Mydi;

class LocatorArrayTest extends AbstractLocatorTest
{

    /**
     * @param $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testArrayParams($name, $value)
    {
        $this->locator[$name] = $value;
        $this->assertSame($value, $this->locator[$name]);
        $this->assertSame(true, isset($this->locator[$name]));
        unset($this->locator[$name]);
        $this->assertSame(false, isset($this->locator[$name]));
    }


    public function testArraySetNameExist()
    {
        $this->locator['test'] = 1;
        $this->assertSame(1, $this->locator['test']);
        $this->locator['test'] = 2;
        $this->assertSame(2, $this->locator['test']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArraySetNameNotString()
    {
        $this->locator[1] = 1;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayDeleteNotExist()
    {
        unset($this->locator['test']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayResolveNameNotExist()
    {
        $this->locator['test'];
    }

    public function testArraySetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\Smpl\Mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator['test'] = $mock;
        $this->assertSame($result, $this->locator['test']);
        $this->assertSame(true, isset($this->locator['test']));
        unset($this->locator['test']);
        $this->assertSame(false, isset($this->locator['test']));
    }
}
