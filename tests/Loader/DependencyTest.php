<?php
namespace SmplTest\Mydi\Loader;

use Smpl\Mydi\Loader\Dependency;
use Smpl\Mydi\Loader\DependencyExecutorInterface;
use Smpl\Mydi\Loader\Executor\Factory;
use Smpl\Mydi\Loader\Executor\Lazy;
use Smpl\Mydi\Loader\Executor\Service;
use Smpl\Mydi\Loader\File\Readerinterface;

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    private static $parsedConfig = [
        'stdClass' => [
            "construct" => ['PDO', 'Another'],
        ],
        'Another' => [],
        'MyInterface' => 'MyClass',
        'test' => ['class' => '\stdClass'],
        'testContainer' => [
            'Executor' => 'containerName'
        ]
    ];
    /**
     * @var Dependency
     */
    private $dependency;

    /**
     * @param $container
     * @param $expected
     * @param $config
     * @dataProvider providerValid
     */
    public function testLoad($container, $expected, $config)
    {
        $executor = $this->getMock(DependencyExecutorInterface::class);
        $executor->expects($this->any())
            ->method('execute')
            ->with($container, self::$parsedConfig[$container])
            ->will($this->returnValue($expected));
        $executors['mock'] = $executor;
        if (is_array($config) && array_key_exists('Executor', $config)) {
            $executors[$config['Executor']] = $executor;
        }

        $mock = $this->getMock(ReaderInterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue(self::$parsedConfig));
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency($mock, 'mock', $executors);

        $result = $this->dependency->load($container);
        $this->assertSame($expected, $result);
    }

    public function providerValid()
    {
        $result = [];
        foreach (self::$parsedConfig as $key => $value) {
            $call = [];
            $call[] = $key;
            $call[] = !is_string($value) && array_key_exists('class', $value) ? $value['class'] : $key;
            $call[] = $value;
            $result[] = $call;
        }
        return $result;
    }

    public function testDefaultExecutors()
    {
        $result = Dependency::getDefaultExecutors();
        $this->assertSame(3, count($result));
        $expecteds = [
            'service' => Service::class,
            'factory' => Factory::class,
            'lazy' => Lazy::class,
        ];
        foreach ($expecteds as $key => $value) {
            $this->assertTrue(array_key_exists($key, $result));
            $this->assertInstanceOf($value, $result[$key]);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container name must be a string
     */
    public function testExecutorNotString()
    {
        $mock = $this->getMock(Readerinterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(['test' => ['executor' => 123]]);
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency($mock);
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor: `magic`, not found
     */
    public function testExecutorNotFound()
    {
        $mock = $this->getMock(Readerinterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(['test' => ['executor' => 'magic']]);
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency($mock);
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Default Executor name must be string
     */
    public function testSetDefaultExecutorNameNotString()
    {
        $mock = $this->getMock(Readerinterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(['test' => ['Executor' => 'magic']]);
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency(
            $mock,
            213
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidConfiguration()
    {
        $mock = $this->getMock(Readerinterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn('123');
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency($mock);
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor name must be a string
     */
    public function testSetExecutorsNotString()
    {
        $mock = $this->getMock(Readerinterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(['test' => ['Executor' => 'magic']]);
        /** @var Readerinterface $mock */
        $executor = $this->getMock(DependencyExecutorInterface::class);
        $this->dependency = new Dependency(
            $mock,
            'valid',
            [123 => $executor]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Excecutor: `test`, must implement DependencyExecutorInterface
     */
    public function testSetExecutorsNotImplementInterface()
    {
        $mock = $this->getMock(Readerinterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(['test' => ['Executor' => 'magic']]);
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency(
            $mock,
            'valid',
            ['test' => 123]
        );
    }

    protected function setUp()
    {
        parent::setUp();
        $mock = $this->getMock(ReaderInterface::class);
        $mock->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue(self::$parsedConfig));
        /** @var Readerinterface $mock */
        $this->dependency = new Dependency($mock);
    }

}
