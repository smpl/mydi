<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\ContainerAwareInterface;
use Smpl\Mydi\ContainerAwareTrait;
use Smpl\Mydi\ContainerException;
use Smpl\Mydi\NotFoundException;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function provide(string $name)
    {
        try {
            $class = new  \ReflectionClass($name);
            if (!$class->isInstantiable()) {
                throw new ContainerException("{$class->name} is not instantiable");
            }
            return $this->createInstance($class);
        } catch (\ReflectionException $e) {
            throw new NotFoundException();
        }
    }

    public function hasProvide(string $name): bool
    {
        $result = false;
        if (class_exists($name)) {
            $result = true;
        }
        return $result;
    }

    private function getDependencies(\ReflectionClass $class): array
    {
        $result = [];
        if (null !== $class->getConstructor()) {
            foreach ($class->getConstructor()->getParameters() as $parameter) {
                $result[$parameter->name] = null !== $parameter->getClass() ? $parameter->getClass()->name : $parameter->name;
            }
        }
        return $result;
    }

    private function createInstance(\ReflectionClass $class)
    {
        $dependencies = $this->getDependencies($class);
        $args = [];
        foreach ($dependencies as $dependency) {
            $args[] = $this->container->get($dependency);
        }
        $result = $class->newInstanceArgs($args);

        if ($result instanceof ContainerAwareInterface) {
            $result->setContainer($this->container);
        }
        return $result;
    }
}
