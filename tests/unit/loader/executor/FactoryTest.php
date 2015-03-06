<?php
namespace smpl\mydi\tests\unit\loader\executor;

use smpl\mydi\loader\executor\Factory;

class FactoryTest extends ServiceTest {
    protected $executorClass = Factory::class;
    protected $wrapperClass = \smpl\mydi\container\Factory::class;
}
