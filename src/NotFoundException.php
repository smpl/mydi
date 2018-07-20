<?php
declare(strict_types=1);

namespace Smpl\Mydi;

use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends \LogicException implements NotFoundExceptionInterface
{

}
