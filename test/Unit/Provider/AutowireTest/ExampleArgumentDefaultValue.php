<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\AutowireTest;

class ExampleArgumentDefaultValue
{
    public $a;

    public function __construct($a = 123)
    {
        $this->a = $a;
    }
}
