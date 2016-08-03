<?php
namespace smpl\mydi\test;

use smpl\mydi\LocatorInterface;

trait LocatorInterfaceTestTrait
{
    use LoaderInterfaceTestTrait;

    /**
     * @return LocatorInterface
     */
    abstract protected function createLocatorInterfaceObject();


}