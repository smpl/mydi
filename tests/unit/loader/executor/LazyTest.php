<?php
namespace smpl\mydi\tests\unit\loader\executor;

use smpl\mydi\loader\executor\Lazy;

class LazyTest extends ServiceTest {
    protected $executorClass = Lazy::class;
    protected $wrapperClass = \smpl\mydi\container\Lazy::class;
    protected $wrapperResult = \Closure::class;
}
