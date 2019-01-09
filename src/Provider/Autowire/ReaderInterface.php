<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use Psr\Container\NotFoundExceptionInterface;

interface ReaderInterface
{
    /**
     * @param string $name
     * @return array
     * @throws NotFoundExceptionInterface
     */
    public function getDependecies(string $name): array;
}
