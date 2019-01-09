<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire\Reader;

use Smpl\Mydi\Provider\Autowire\AbstractReflection;
use Smpl\Mydi\Provider\Autowire\ReaderInterface;

class Reflection implements ReaderInterface
{
    public function getDependecies(string $name): array
    {
        return AbstractReflection::readDependencies($name);
    }
}
