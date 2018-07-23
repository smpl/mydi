<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \LogicException implements NotFoundExceptionInterface
{

}
