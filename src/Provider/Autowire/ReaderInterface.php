<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

use Smpl\Mydi\Exception\NotFoundInterface;

interface ReaderInterface
{
    /**
     * @param string $name
     * @return array
     * @throws NotFoundInterface
     */
    public function getDependecies(string $name): array;
}
