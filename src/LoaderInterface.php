<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\ContainerInterface;

interface LoaderInterface
{
    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function load(ContainerInterface $container);
}
