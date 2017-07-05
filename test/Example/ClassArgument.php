<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Example;

class ClassArgument
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

}