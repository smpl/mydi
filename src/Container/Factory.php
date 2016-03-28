<?php
namespace Smpl\Mydi\Container;

use Smpl\Mydi\ContainerInterface;
use Smpl\Mydi\LocatorInterface;

/**
 * Class Factory
 * @package Smpl\Mydi\Container
 * @see https://github.com/smpl/mydi/issues/2
 */
class Factory implements ContainerInterface
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

    public function resolve(LocatorInterface $locator)
    {
        return call_user_func_array($this->callback, [$locator]);
    }
}