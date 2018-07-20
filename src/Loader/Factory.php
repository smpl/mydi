<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

class Factory implements LoaderInterface
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public static function fromReflectionClass(\ReflectionClass $class, array $dependencies = [])
    {
        return new static(function (ContainerInterface $container) use ($class, $dependencies) {
            $args = [];
            foreach ($dependencies as $dependency) {
                $args[] = $container->get($dependency);
            }
            return $class->newInstanceArgs($args);
        });
    }

    public function get(ContainerInterface $container)
    {
        return call_user_func_array($this->callback, [$container]);
    }
}
