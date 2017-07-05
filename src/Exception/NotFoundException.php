<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends \LogicException implements NotFoundExceptionInterface
{

}