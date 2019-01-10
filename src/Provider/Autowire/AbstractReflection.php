<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use Smpl\Mydi\Exception\NotFound;

abstract class AbstractReflection
{
    /**
     * @param string $name
     * @return array
     * @throws NotFoundExceptionInterface
     */
    public static function readDependencies(string $name): array
    {
        try {
            $class = new ReflectionClass($name);
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
        } catch (ReflectionException $e) {
            throw new NotFound($name);
        }
    }
}
