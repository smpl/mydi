<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use ReflectionClass;
use ReflectionException;

abstract class AbstractReflection
{
    /**
     * @param string $name
     * @return array
     * @throws ReflectionException
     */
    public static function readDependencies(string $name): array
    {
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
    }
}
