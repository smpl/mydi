<?php
namespace Smpl\Mydi\Loader;

use Interop\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

final class Factory implements LoaderInterface
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

    public function get(ContainerInterface $locator)
    {
        return call_user_func_array($this->callback, [$locator]);
    }
}