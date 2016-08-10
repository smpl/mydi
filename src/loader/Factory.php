<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

/**
 * Class Factory
 */
class Factory implements LoaderInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param \Closure $callback Анонимная функция которая возвращает необходимый результат
     */
    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    public function get(LocatorInterface $locator)
    {
        return call_user_func_array($this->callback, [$locator]);
    }
}