<?php
namespace smpl\mydi;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    protected function setUp()
    {
        parent::setUp();
        $this->locator = new Locator();
    }


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

    /**
     * @param $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testPropertyParams($name, $value)
    {
        $this->locator->$name = $value;
        $this->assertSame($value, $this->locator->$name);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameExist()
    {
        $this->locator->add('test', 1);
        $this->locator->add('test', 1);
    }

    public function testArraySetNameExist()
    {
        $this->locator['test'] = 1;
        $this->assertSame(1, $this->locator['test']);
        $this->locator['test'] = 2;
        $this->assertSame(2, $this->locator['test']);
    }

    public function testPropertySetNameExist()
    {
        $this->locator->test = 1;
        $this->assertSame(1, $this->locator->test);
        $this->locator->test = 2;
        $this->assertSame(2, $this->locator->test);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameNotString()
    {
        $this->locator->add(1, 1);
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
    public function testDeleteNotExist()
    {
        $this->locator->delete('test');
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
    public function testResolveNameNotExist()
    {
        $this->locator->resolve('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayResolveNameNotExist()
    {
        $this->locator['test'];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPropertyResolveNameNotExist()
    {
        $this->locator['test'];
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
    }

    public function testArraySetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator['test'] =  $mock;
        $this->assertSame($result, $this->locator['test']);
        $this->assertSame(true, isset($this->locator['test']));
        unset($this->locator['test']);
        $this->assertSame(false, isset($this->locator['test']));
    }

    public function testPropertySetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator->test =  $mock;
        $this->assertSame($result, $this->locator->test);
    }

    public function providerValidParams()
    {
        return [
            ['int', 1],
            ['float', 0.5],
            ['bool', true],
            ['string', 'test'],
            ['object', new \stdClass()],
        ];
    }
}
 