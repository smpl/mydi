<?php

use Smpl\Mydi\Container\Factory;

return new Factory(function () {
    static $a = 0;
    return ++$a;
});