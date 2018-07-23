<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\NotFoundException;
use Smpl\Mydi\Provider\Autowire\ReflectionClass;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    public function get(string $name)
    {
        try {
            $class = new ReflectionClass($name);
            return new Service($class->createClosure());
        } catch (\ReflectionException $e) {
            throw new NotFoundException();
        }
    }

    public function has(string $name): bool
    {
        $result = false;
        if (class_exists($name)) {
            $result = true;
        }
        return $result;
    }
}
