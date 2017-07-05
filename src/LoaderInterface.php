<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\ContainerInterface;

interface LoaderInterface
{
    /**
     * Получает значение
     * @param ContainerInterface $locator
     * @return mixed
     */
    public function get(ContainerInterface $locator);
}