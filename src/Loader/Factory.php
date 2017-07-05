<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

final class Factory implements LoaderInterface
{
    /**
     * @var callable
     */
    private $callback;

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