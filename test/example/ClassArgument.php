<?php
namespace smpl\mydi\test\example;

class ClassArgument
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

}