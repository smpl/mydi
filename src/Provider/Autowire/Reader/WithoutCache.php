<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire\Reader;

use ReflectionException;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Provider\Autowire\AbstractReflection;
use Smpl\Mydi\Provider\Autowire\ReaderInterface;

class WithoutCache implements ReaderInterface
{
    public function getDependecies(string $name): array
    {
        try {
            return AbstractReflection::readDependencies($name);
        } catch (ReflectionException $e) {
            throw new NotFound($name);
        }
    }
}
