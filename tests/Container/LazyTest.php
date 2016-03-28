<?php
namespace Smpl\Mydi\Container;

use Smpl\Mydi\LocatorInterface;

class LazyTest extends \PHPUnit_Framework_TestCase
{

    public function testResolve()
    {
        $lazy = new Lazy(function () {
            return new \stdClass();
        });
        /** @var LocatorInterface $locator */
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $container = $lazy->resolve($locator);
        $this->assertSame(true, is_callable($container));
        $this->assertSame(true, $container instanceof \Closure);
        $result = $container();
        $this->assertSame(true, $result instanceof \stdClass);
        $this->assertNotSame($result, $container());
    }

    public function testClosingWithParam()
    {
        $std = new \StdClass;
        $std->param = 1;
        $locator = $this->getMockBuilder(LocatorInterface::class)
            ->getMock();
        $locator->expects($this->any())
            ->method('resolve')
            ->with($this->stringContains('std'))
            ->willReturn($std);

        /** @var LocatorInterface $locator */
        $lazy = new Lazy(function (LocatorInterface $locator, $param) {
            $locator->resolve('std')->param = $param;
            return $locator->resolve('std');
        });
        $container = $lazy->resolve($locator);
        $this->assertSame(1, $locator->resolve('std')->param);
        $result = $container(5);
        $this->assertSame(5, $result->param);
    }


}
 