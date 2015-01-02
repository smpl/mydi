<?php
namespace smpl\mydi\container;

/**
 * Class Lazy
 * @package smpl\mydi\container
 * @see https://github.com/smpl/mydi/issues/9
 */
class Lazy extends Factory
{
    public function resolve()
    {
        return function () {
            return call_user_func_array($this->callback, func_get_args());
        };
    }
}