<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

use Psr\Container\ContainerExceptionInterface;

class NameNotString extends \RuntimeException implements ContainerExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Container name must be string');
    }
}
