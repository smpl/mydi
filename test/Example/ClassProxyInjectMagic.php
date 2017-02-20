<?php
namespace Smpl\Mydi\Test\Example;

class ClassProxyInjectMagic
{
    /**
     * @var ClassEmpty
     */
    private $example;

    /**
     * ExampleProxy constructor.
     * @param \stdClass $e
     * @magic Smpl\Mydi\Test\Example\ClassStd $e
     */
    public function __construct(\stdClass $e)
    {
        $this->example = $e;
    }
}