<?php
namespace Smpl\Mydi\Exception;

use Psr\Container\ContainerExceptionInterface;

final class ContainerException extends \LogicException implements ContainerExceptionInterface
{

}