<?php
namespace smpl\mydi\container;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $factory = new Service(function () {
            return new \stdClass();
        });
        $result = $factory->resolve();
        $this->assertSame($result, $factory->resolve());
    }

    public function testClosing()
    {
        $result = 123;
        $factory = new Service(function () use ($result) {
            return $result;
        });
        $this->assertSame($result, $factory->resolve());
    }
}
 