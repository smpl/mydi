<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\ContainerInterface;

interface LoaderInterface
{
    public function load(ContainerInterface $container);
}
