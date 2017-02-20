<?php
namespace Smpl\Mydi\Test\Example;

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