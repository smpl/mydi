<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Example;

class ClassProxyInjected
{
    /**
     * @var ClassEmpty
     */
    private $example;

    /**
     * ExampleProxy constructor.
     * @param \stdClass $e
     * @inject Smpl\Mydi\Test\Example\ClassStd $e
     */
    public function __construct(\stdClass $e)
    {
        $this->example = $e;
    }
}