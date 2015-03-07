<?php
namespace smpl\mydi\loader;

use smpl\mydi\LocatorInterface;

interface LocatorAwareInterface
{
    public static function mydiLoad(LocatorInterface $locator);
}