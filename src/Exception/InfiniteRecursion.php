<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

use Psr\Container\ContainerExceptionInterface;

class InfiniteRecursion extends \RuntimeException implements ContainerExceptionInterface
{
    public function __construct(string $name, array $calls)
    {
        $calls = implode(', ', $calls);
        $message = "Infinite recursion in the configuration, name called again: $name, call stack: $calls.";
        parent::__construct($message);
    }

}
