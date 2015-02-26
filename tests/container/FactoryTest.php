<?php
namespace smpl\mydi\container;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $factory = new Factory(function (){
            return new \stdClass();
        });
        $result = $factory->resolve();
        $this->assertNotSame($result, $factory->resolve());
    }

    public function testClosing()
    {
        $result = 123;
        $factory = new Factory(function () use ($result) {
            return $result;
        });
        $this->assertSame($result, $factory->resolve());
    }
}
 