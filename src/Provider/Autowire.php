<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use ReflectionException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire\ReflectionClass;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    public function provide(string $name)
    {
        try {
            $class = new ReflectionClass($name);
            return Service::fromClassName($name, $class->getConstructDependencies());
        } catch (ReflectionException $exception) {
            throw new NotFound($name);
        }
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }
}
