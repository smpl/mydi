<?php
namespace smpl\mydi\tests\unit\loader;

use smpl\mydi\loader\Manager;
use smpl\mydi\LoaderInterface;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    private $manager;

    public function testIsLoadable()
    {
        $this->assertFalse($this->manager->isLoadable('false'));
        $loader = $this->getMock(LoaderInterface::class);
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with('true')
            ->will($this->returnValue(true));
        /** @var LoaderInterface $loader */
        $this->manager->attach($loader);
        $this->assertTrue($this->manager->isLoadable('true'));
    }

    public function testLoad()
    {
        $loader = $this->getMock(LoaderInterface::class);
        $loader->expects($this->once())
            ->method('isLoadable')
            ->with('valid')
            ->will($this->returnValue(true));
        $loader->expects($this->once())
            ->method('load')
            ->with('valid')
            ->will($this->returnValue(1234));
        /** @var LoaderInterface $loader */
        $this->manager->attach($loader);
        $this->assertSame(1234, $this->manager->load('valid'));
        $this->assertTrue($this->manager->contains($loader));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`test`, must be loadable
     */
    public function testLoadInvalid()
    {
        $this->manager->load('test');
    }

    public function testGetAllLoadableName()
    {
        $this->assertSame([], $this->manager->getAllLoadableName());

        $loader = $this->getMock(LoaderInterface::class);
        $loader->expects($this->any())
            ->method('getAllLoadableName')
            ->will($this->returnValue(['alo', 'magic']));
        /** @var LoaderInterface $loader */
        $this->manager->attach($loader);
        $this->assertSame(['alo', 'magic'], $this->manager->getAllLoadableName());

        $loader2 = $this->getMock(LoaderInterface::class);
        $loader2->expects($this->once())
            ->method('getAllLoadableName')
            ->will($this->returnValue(['magic', 'c']));
        /** @var LoaderInterface $loader2 */
        $this->manager->attach($loader2);
        $this->assertSame(['alo', 'magic', 'c'], $this->manager->getAllLoadableName());
    }

    public function testLoadFromArray()
    {
        $loader = $this->getMock(LoaderInterface::class);
        /** @var LoaderInterface $loader */
        $this->manager = new Manager([$loader]);
        $this->assertTrue($this->manager->contains($loader));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->manager = new Manager();
    }

}
