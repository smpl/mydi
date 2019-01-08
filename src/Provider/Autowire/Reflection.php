<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use ReflectionClass;

class Reflection extends ReflectionClass
{
    public function getDependencies(): array
    {
        $result = [];
        if (null !== $this->getConstructor()) {
            foreach ($this->getConstructor()->getParameters() as $parameter) {
                $result[$parameter->name] = $parameter->name;
                if (null !== $parameter->getClass()) {
                    $result[$parameter->name] = $parameter->getClass()->name;
                }
            }
        }
        return $result;
    }
}
