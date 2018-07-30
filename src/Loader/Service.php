<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Closure;
use Psr\Container\ContainerInterface;

class Service
{
    private $result;
    private $isCalled = false;
    private $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function load(ContainerInterface $container)
    {
        if (!$this->isCalled) {
            $this->result = call_user_func_array($this->closure, [$container]);
            $this->isCalled = true;
        }
        return $this->result;
    }
}
