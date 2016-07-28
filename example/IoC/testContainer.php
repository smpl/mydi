<?php

use smpl\mydi\container\Factory;

return new Factory(function () {
    static $a = 0;
    return ++$a;
});