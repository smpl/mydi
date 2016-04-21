<?php

namespace SmplExample\Mydi\Locator;

class A
{
    private $b;
    public function __construct(B $b)
    {
        $this->b = $b;
    }

}