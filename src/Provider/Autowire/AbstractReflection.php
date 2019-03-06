<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use ReflectionException;
use ReflectionMethod;

abstract class AbstractReflection
{
    /**
     * @param string $name
     * @return array
     * @throws ReflectionException
     */
    public static function readDependencies(string $name): array
    {
        /** @psalm-suppress TypeCoercion */
        $class = new \ReflectionClass($name);
        $result = [];
        if (null !== $class->getConstructor()) {
            /** @psalm-suppress PossiblyNullArgument */
            $result = self::readFromConstructor($class->getConstructor());
        }
        return $result;
    }

    private static function readFromConstructor(ReflectionMethod $constructor): array
    {
        $result = [];
        foreach ($constructor->getParameters() as $parameter) {
            $result[$parameter->name] = $parameter->name;
            if (null !== $parameter->getClass()) {
                /** @psalm-suppress PossiblyNullPropertyFetch */
                $result[$parameter->name] = $parameter->getClass()->name;
            }
        }
        return $result;
    }
}
