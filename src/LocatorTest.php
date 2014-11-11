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
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddNameExist()
    {
        $this->locator->add('test', 1);
        $this->locator->add('test', 1);
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
    public function testResolveNameNotExist()
    {
        $this->locator->resolve('test');
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
 