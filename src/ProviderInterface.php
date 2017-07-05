<?php
declare(strict_types=1);

namespace Smpl\Mydi;

/**
 * Этот интерфейс используется для расширенеия возможностей Smpl\Mydi\Container
 */
interface ProviderInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}