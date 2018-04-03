<?php

namespace Smpl\Mydi\Test\Example;

class UserCustomPDO
{
    public $customPDO;

    public function __construct(CustomPDO $magic)
    {
        $this->customPDO = $magic;
    }

}
