<?php
namespace Smpl\Mydi\Container;

use Smpl\Mydi\LocatorInterface;

/**
 * Class Lazy
 * @package Smpl\Mydi\Container
 * @see https://github.com/smpl/mydi/issues/9
 */
class Lazy extends Factory
{
    public function resolve(LocatorInterface $locator)
    {
        return function () use ($locator) {
            $args = array_merge([$locator], func_get_args());
            return call_user_func_array($this->callback, $args);
        };
    }
}