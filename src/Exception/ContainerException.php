<?php
declare(strict_types=1);

namespace Smpl\Mydi\Exception;

use Psr\Container\ContainerExceptionInterface;

final class ContainerException extends \LogicException implements ContainerExceptionInterface
{

}