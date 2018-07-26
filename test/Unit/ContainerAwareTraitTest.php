<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\ContainerAwareTrait;

class ContainerAwareTraitTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testSetContainer()
    {
        /** @var ContainerAwareTrait $trait */
        $trait = $this->getMockForTrait(ContainerAwareTrait::class);
        $trait->setContainer($this->createMock(ContainerInterface::class));
        $this->assertAttributeNotEmpty('container', $trait);
    }
}
