<?php

use smpl\mydi\loader\Factory;

return new Factory(function () {
    static $a = 0;
    return ++$a;
});