<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\Autowire;

use PHPUnit\Framework\TestCase;
use Smpl\Mydi\Provider\Autowire\Reader;

class ReaderTest extends TestCase
{
    /**
     * @var Reader
     */
    private $reader;

    public function testIsFactorySuccess()
    {
        $this->assertTrue($this->reader->isFactory(<<<'Magic'
/**
 * @factory
 */
Magic
        ));
    }

    public function testIsFactoryFail()
    {
        $this->assertFalse($this->reader->isFactory(''));
    }

    public function testGetAliasNameSuccess()
    {
        $this->assertSame(\stdClass::class, $this->reader->getAliasName(<<<'Magic'
/**
 * @alias \stdClass
 */
Magic
        ));
    }

    public function testGetAliasNameFail()
    {
        $this->assertFalse($this->reader->getAliasName(''));
    }

    public function testGetDependeciesWithoutParams()
    {
        $this->assertSame([], $this->reader->getDependencies(''));
    }

    public function testGetDependeciesParamName()
    {
        $param = $this->createMock(\ReflectionParameter::class);
        $param->method('getName')
            ->willReturn('magic');
        /** @var \ReflectionParameter $param */

        $this->assertSame(['magic' => 'magic'], $this->reader->getDependencies('', $param));
    }

    public function testGetDependeciesParamType()
    {
        $param = $this->createMock(\ReflectionParameter::class);
        $param->method('getName')
            ->willReturn('magic');
        $class = $this->createMock(\ReflectionClass::class);
        $class->method('getName')
            ->willReturn('foo');
        $param->method('getClass')
            ->willReturn($class);
        /** @var \ReflectionParameter $param */

        $this->assertSame(['magic' => 'foo'], $this->reader->getDependencies('', $param));
    }

    public function testGetDependenciesParamInject()
    {
        $param = $this->createMock(\ReflectionParameter::class);
        $param->method('getName')
            ->willReturn('magic');
        /** @var \ReflectionParameter $param */

        $comment = <<<'Magic'
/**
 * @inject \stdClass $magic
 */
Magic;
        $this->assertSame(['magic' => \stdClass::class], $this->reader->getDependencies($comment, $param));
    }

    public function testMagic()
    {
        $this->assertTrue(true);
    }

    protected function setUp()
    {
        $this->reader = new Reader;
    }
}
