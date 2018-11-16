<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\NotFoundException;

class Reflection implements LoaderInterface
{
    private $isLoaded = false;
    private $result;
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function load(ContainerInterface $container)
    {
        if (!$this->isLoaded) {
            $this->isLoaded = true;
            $this->result = $this->createInstance($container);
        }
        return $this->result;
    }

    private function createInstance(ContainerInterface $container)
    {
        try {
            $class = new \ReflectionClass($this->className);
            $arguments = [];
            foreach ($this->getDependencies($class) as $dependency) {
                $arguments[] = $container->get($dependency);
            }
            return $class->newInstanceArgs($arguments);
        } catch (\ReflectionException $e) {
            throw new NotFoundException();
        }
    }

    private function getDependencies(\ReflectionClass $class): array
    {
        $result = [];
        if (null !== $class->getConstructor()) {
            foreach ($class->getConstructor()->getParameters() as $parameter) {
                $result[$parameter->name] = $parameter->name;
                if (null !== $parameter->getClass()) {
                    $result[$parameter->name] = $parameter->getClass()->name;
                }
            }
        }
        return $result;
    }
}
