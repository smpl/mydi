<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire\Reader\Reflection;
use Smpl\Mydi\Provider\Autowire\ReaderInterface;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    private $reader;

    public function __construct()
    {
        $this->reader = new Reflection();
    }

    public function provide(string $name)
    {
        $dependencies = $this->reader->getDependecies($name);
        $closure = function (ContainerInterface $container) use ($dependencies, $name) {
            $arguments = [];
            foreach ($dependencies as $dependency) {
                $arguments[] = $container->get($dependency);
            }
            return new $name(... $arguments);
        };
        return new Service($closure);
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }

    public function setReader(ReaderInterface $reader): self
    {
        $this->reader = $reader;
        return $this;
    }
}
