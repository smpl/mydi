<?php
namespace smpl\mydi\test\example;

class ClassProxyInjected
{
    /**
     * @var ClassEmpty
     */
    private $example;

    /**
     * ExampleProxy constructor.
     * @param \stdClass $e
     * @inject smpl\mydi\test\example\ClassStd $e
     */
    public function __construct(\stdClass $e)
    {
        $this->example = $e;
    }
}