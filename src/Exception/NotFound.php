<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

class NotFound extends \RuntimeException implements NotFoundInterface
{
    public function __construct(string $name)
    {
        parent::__construct("Container: `$name`, is not defined");
    }
}
