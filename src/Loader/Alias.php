<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

class Alias implements LoaderInterface
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get(ContainerInterface $container)
    {
        return $container->get($this->name);
    }
}
