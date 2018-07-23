<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

class Factory implements LoaderInterface
{
    private $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function get(ContainerInterface $container)
    {
        return call_user_func_array($this->closure, [$container]);
    }
}
