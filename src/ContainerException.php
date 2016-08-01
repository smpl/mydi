<?php
namespace smpl\mydi;

use SebastianBergmann\CodeCoverage\RuntimeException;

class ContainerException extends RuntimeException implements \Interop\Container\Exception\ContainerException
{

}