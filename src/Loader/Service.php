<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Closure;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

class Service implements LoaderInterface
{
    /**
     * @var mixed
     */
    private $result;
    /**
     * @var bool
     */
    private $isCalled = false;
    /**
     * @var Closure
     */
    private $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public static function fromClassName(string $name, array $dependencies)
    {
        return new static(function (ContainerInterface $container) use ($dependencies, $name): object {
            $arguments = [];
            foreach ($dependencies as $dependency) {
                $arguments[] = $container->get($dependency);
            }
            return new $name(...$arguments);
        });
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
