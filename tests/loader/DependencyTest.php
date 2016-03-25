<?php
namespace smpl\mydi\loader;

use smpl\mydi\loader\executor\Factory;
use smpl\mydi\loader\executor\Lazy;
use smpl\mydi\loader\executor\Service;

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
            'executor' => 'containerName'
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
        if (is_array($config) && array_key_exists('executor', $config)) {
            $executors[$config['executor']] = $executor;
        }
        $this->dependency = new Dependency(
            function () {
                return self::$parsedConfig;
            },
            'mock',
            $executors
        );

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
        $this->dependency = new Dependency(function () {
            return ['test' => ['executor' => 123]];
        });
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor: `magic`, not found
     */
    public function testExecutorNotFound()
    {
        $this->dependency = new Dependency(function () {
            return ['test' => ['executor' => 'magic']];
        });
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Default executor name must be string
     */
    public function testSetDefaultExecutorNameNotString()
    {
        $this->dependency = new Dependency(
            function () {
            },
            213
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidConfiguration()
    {
        $this->dependency = new Dependency(function () {
            return '123';
        });
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor name must be a string
     */
    public function testSetExecutorsNotString()
    {
        $executor = $this->getMock(DependencyExecutorInterface::class);
        $this->dependency = new Dependency(
            function () {
            },
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
        $this->dependency = new Dependency(
            function () {
            },
            'valid',
            ['test' => 123]
        );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->dependency = new Dependency(function () {
            return self::$parsedConfig;
        });
    }

}
