<?php
namespace smpl\mydi\container;

class Lazy extends Factory
{
    public function resolve()
    {
        return function () {
            return call_user_func_array($this->callback, func_get_args());
        };
    }
}