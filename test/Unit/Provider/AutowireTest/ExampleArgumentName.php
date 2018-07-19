<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\AutowireTest;

class ExampleArgumentName
{
    public $a;

    public function __construct($a)
    {
        $this->a = $a;
    }

}
