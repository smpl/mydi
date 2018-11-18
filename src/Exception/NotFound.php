<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFound extends \RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(string $name)
    {
        parent::__construct("Container: `$name`, is not defined");
    }
}
