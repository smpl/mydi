<?php
namespace Smpl\Mydi\Loader;

use Smpl\Mydi\LocatorInterface;

interface LocatorAwareInterface
{
    public static function mydiLoad(LocatorInterface $locator);
}