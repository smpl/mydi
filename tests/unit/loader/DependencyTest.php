<?php
namespace smpl\mydi\tests\unit\loader;

use smpl\mydi\loader\Dependency;
use smpl\mydi\loader\DependencyExecutorInterface;
use smpl\mydi\loader\executor\Factory;
use smpl\mydi\loader\executor\Lazy;
use smpl\mydi\loader\executor\Service;
use smpl\mydi\loader\ParserInterface;

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
        $this->dependency->setExecutors($executors);
        $this->dependency->setDefaultExecutorName('mock');
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
        $this->assertSame($result, $this->dependency->getExecutors());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container name must be a string
     */
    public function testExecutorNotString()
    {
        $parser = $this->getMock(ParserInterface::class);
        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue(['test' => ['executor' => 123]]));
        /** @var ParserInterface $parser */
        $this->dependency->setParser($parser);
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor: `magic`, not found
     */
    public function testExecutorNotFound()
    {
        $parser = $this->getMock(ParserInterface::class);
        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue(['test' => ['executor' => 'magic']]));
        /** @var ParserInterface $parser */
        $this->dependency->setParser($parser);
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Default executor name must be string
     */
    public function testSetDefaultExecutorNameNotString()
    {
        $this->dependency->setDefaultExecutorName(123);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidConfiguration()
    {
        $parser = $this->getMock('smpl\mydi\loader\ParserInterface');
        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue('123'));
        /** @var ParserInterface $parser */
        $this->dependency->setParser($parser);
        $this->dependency->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor name must be a string
     */
    public function testSetExecutorsNotString()
    {
        $executor = $this->getMock(DependencyExecutorInterface::class);
        $this->dependency->setExecutors([123 => $executor]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Excecutor: `test`, must implement DependencyExecutorInterface
     */
    public function testSetExecutorsNotImplementInterface()
    {
        $this->dependency->setExecutors(['test' => 123]);
    }

    protected function setUp()
    {
        parent::setUp();
        $parser = $this->getMock(ParserInterface::class);
        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue(self::$parsedConfig));
        /** @var ParserInterface $parser */
        $this->dependency = new Dependency($parser);
    }

}
