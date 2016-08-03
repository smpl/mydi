<?php
namespace smpl\mydi;

use Interop\Container\Exception\NotFoundException as NotFoundInteropException;

class NotFoundException extends \RuntimeException implements NotFoundInteropException
{

}