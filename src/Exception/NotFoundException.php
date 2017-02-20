<?php
namespace Smpl\Mydi\Exception;

use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends \LogicException implements NotFoundExceptionInterface
{

}