<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\ContainerException;

class ReflectionClass extends \ReflectionClass
{

    public function createClosure(): callable
    {
        if (!$this->isInstantiable()) {
            throw new ContainerException("{$this->name} is not instantiable");
        }
        $dependencies = $this->getDependencies();
        $class = $this;
        return function (ContainerInterface $container) use ($class, $dependencies) {
            $args = [];
            foreach ($dependencies as $dependency) {
                $args[] = $container->get($dependency);
            }
            return $class->newInstanceArgs($args);
        };
    }

    private function getDependencies(): array
    {
        $result = [];
        if (null !== $this->getConstructor()) {
            foreach ($this->getConstructor()->getParameters() as $parameter) {
                $result[$parameter->name] = null !== $parameter->getClass() ? $parameter->getClass()->name : $parameter->name;
            }
        }
        return $result;
    }

}
