<?php
namespace smpl\mydi\container;

use smpl\mydi\ContainerInterface;

class Factory implements ContainerInterface
{
    /**
     * @var callable
     */
    protected $callback;

    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    public function resolve()
    {
        return call_user_func_array($this->callback, []);
    }
}