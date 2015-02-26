<?php
namespace smpl\mydi\container;

class LazyTest extends \PHPUnit_Framework_TestCase
{

    public function testResolve()
    {
        $lazy = new Lazy(function () {
            return new \stdClass();
        });
        $container = $lazy->resolve();
        $this->assertSame(true, is_callable($container));
        $this->assertSame(true, $container instanceof \Closure);
        $result = $container();
        $this->assertSame(true, $result instanceof \stdClass);
        $this->assertNotSame($result, $container());
    }

    public function testClosing()
    {
        $stdClass = new \stdClass();
        $stdClass->test = 1;
        $lazy = new Lazy(function () use ($stdClass) {
            $stdClass->test++;
            return $stdClass;
        });
        $container = $lazy->resolve();
        $this->assertSame(1, $stdClass->test);
        $result = $container();
        $this->assertSame(2, $result->test);
    }

    public function testClosingWithParam()
    {
        $stdClass = new \stdClass();
        $stdClass->param = 1;
        $lazy = new Lazy(function ($param) use ($stdClass) {
            $stdClass->param = $param;
            return $stdClass;
        });
        $container = $lazy->resolve();
        $this->assertSame(1, $stdClass->param);
        $result = $container(5);
        $this->assertSame(5, $result->param);
    }


}
 