<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\Loader\Factory;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\Provider\Autowire;
use Smpl\Mydi\Provider\Autowire\Reader;

class AutowireTest extends TestCase
{

    public function testHas()
    {
        $reader = $this->createMock(Autowire\Reader::class);
        /** @var Reader $reader */
        $autowire = new Autowire($reader);

        $this->assertTrue($autowire->has(\stdClass::class));
        $this->assertFalse($autowire->has('some invalid class name'));
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetInvalid()
    {
        $reader = $this->createMock(Autowire\Reader::class);
        /** @var Reader $reader */
        $autowire = new Autowire($reader);
        $autowire->get('some invalid class name');
    }

    public function testCreateWithoutArguments()
    {
        $autowire = new Autowire();
        $this->assertInstanceOf(Autowire::class, $autowire);
    }

    public function testGetWithoutContructor()
    {
        $reader = $this->createMock(Autowire\Reader::class);
        $reader->expects($this->once())
            ->method('getAliasName')
            ->willReturn(false);
        /** @var Reader $reader */
        $autowire = new Autowire($reader);

        /** @var LoaderInterface $loader */
        $loader = $autowire->get(\stdClass::class);
        $this->assertEquals(Service::class, get_class($loader));
    }

    public function testGetAlias()
    {
        $reader = $this->createMock(Autowire\Reader::class);
        $reader->expects($this->once())
            ->method('getAliasName')
            ->willReturn('magic');
        /** @var Reader $reader */
        $autowire = new Autowire($reader);

        $loader = $autowire->get(\stdClass::class);
        $this->assertEquals(Alias::class, get_class($loader));
    }

    public function testGetFactory()
    {
        $reader = $this->createMock(Autowire\Reader::class);
        $reader->expects($this->once())
            ->method('getAliasName')
            ->willReturn(false);
        $reader->expects($this->once())
            ->method('isFactory')
            ->willReturn(true);
        /** @var Reader $reader */
        $autowire = new Autowire($reader);

        $loader = $autowire->get(\stdClass::class);
        $this->assertEquals(Factory::class, get_class($loader));
    }

    public function testGetWithLoadDependency()
    {
        $reader = $this->createMock(Autowire\Reader::class);
        $reader->expects($this->once())
            ->method('getAliasName')
            ->willReturn(false);
        /** @var Reader $reader */
        $autowire = new Autowire($reader);

        $this->assertSame(Service::class, get_class($autowire->get(\Exception::class)));
    }

}
