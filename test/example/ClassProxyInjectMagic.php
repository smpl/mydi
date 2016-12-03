<?php
namespace smpl\mydi\test\example;

class ClassProxyInjectMagic
{
    /**
     * @var ClassEmpty
     */
    private $example;

    /**
     * ExampleProxy constructor.
     * @param \stdClass $e
     * @magic smpl\mydi\test\example\ClassStd $e
     */
    public function __construct(\stdClass $e)
    {
        $this->example = $e;
    }
}