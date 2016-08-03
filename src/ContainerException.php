<?php
namespace smpl\mydi;

use Interop\Container\Exception\ContainerException as ContainerInteropException;
use SebastianBergmann\CodeCoverage\RuntimeException;

class ContainerException extends RuntimeException implements ContainerInteropException
{

}