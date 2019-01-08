<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Psr\Container\ContainerInterface;
use ReflectionException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire\Reflection;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{

    public function provide(string $name)
    {
        try {
            $dependencies = (new Reflection($name))->getDependencies();
            return new Service(function (ContainerInterface $container) use ($dependencies, $name) {
                $arguments = [];
                foreach ($dependencies as $dependency) {
                    $arguments[] = $container->get($dependency);
                }
                return new $name(... $arguments);
            });
        } catch (ReflectionException $e) {
            throw new NotFound($name);
        }
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }
}
