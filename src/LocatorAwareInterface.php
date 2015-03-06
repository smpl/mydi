<?php
namespace smpl\mydi;

interface LocatorAwareInterface
{
    public static function mydiLoad(LocatorInterface $locator);
}