<?php
declare(strict_types=1);

namespace Smpl\Mydi;

interface ProviderInterface
{
    /**
     * @param string $name
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function provide(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasProvide(string $name): bool;
}
