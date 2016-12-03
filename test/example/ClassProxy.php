<?php
namespace smpl\mydi\test\example;

class ClassProxy
{
    /**
     * @var ClassStd
     */
    private $example;

    public function __construct(ClassStd $e)
    {
        $this->example = $e;
    }
}