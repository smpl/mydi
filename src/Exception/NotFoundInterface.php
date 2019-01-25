<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

use Psr\Container\NotFoundExceptionInterface;

interface NotFoundInterface extends NotFoundExceptionInterface, \Throwable
{

}
