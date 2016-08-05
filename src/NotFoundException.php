<?php
namespace smpl\mydi;

use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;

class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{

}