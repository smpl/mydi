<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\AutowireTest;

class ExampleArgumentBaseType
{
    public $a;

    public function __construct(string $a)
    {
        $this->a = $a;
    }

}
