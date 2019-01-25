<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Smpl\Mydi\Exception\NotFoundInterface;

interface ProviderInterface
{
    /**
     * @param string $name
     * @return mixed
     * @throws NotFoundInterface
     */
    public function provide(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasProvide(string $name): bool;
}
