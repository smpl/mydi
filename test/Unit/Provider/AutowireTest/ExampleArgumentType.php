<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\AutowireTest;

class ExampleArgumentType
{
    public $name;

    public function __construct(\stdClass $name)
    {
        $this->name = $name;
    }

}
