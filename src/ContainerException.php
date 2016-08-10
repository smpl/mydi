<?php
namespace smpl\mydi;

use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;

class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{

}