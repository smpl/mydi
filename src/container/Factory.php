<?php
namespace smpl\mydi\container;

use smpl\mydi\ContainerInterface;

/**
 * Class Factory
 * @package smpl\mydi\container
 * @see https://github.com/smpl/mydi/issues/2
 */
class Factory implements ContainerInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param callable $callback Анонимная функция которая возвращает необходимый результат
     */
    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    public function resolve()
    {
        return call_user_func_array($this->callback, []);
    }
}