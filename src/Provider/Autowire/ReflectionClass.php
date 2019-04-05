<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use ReflectionMethod;

class ReflectionClass extends \ReflectionClass
{
    public function getConstructDependencies(): array
    {
        $constructor = $this->getConstructor();
        if (null === $constructor) {
            return [];
        }
        return self::readContructor($constructor);
    }

    private static function readContructor(ReflectionMethod $constructor): array
    {
        $result = [];
        foreach ($constructor->getParameters() as $parameter) {
            $result[$parameter->name] = $parameter->name;
            $class = $parameter->getClass();
            if (null !== $class) {
                $result[$parameter->name] = $class->name;
            }
        }
        return $result;
    }
}
