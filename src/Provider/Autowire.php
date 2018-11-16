<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Loader\Reflection;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{

    public function provide(string $name)
    {
        return new Reflection($name);
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }

}
