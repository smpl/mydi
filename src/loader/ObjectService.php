<?php
namespace smpl\mydi\loader;

use smpl\mydi\LocatorInterface;

class ObjectService extends ObjectFactory
{
    private $isLoaded = false;
    private $result;

    public function get(LocatorInterface $locator)
    {
        if ($this->isLoaded === false) {
            $this->result = parent::get($locator);
            $this->isLoaded = true;
        }
        return $this->result;
    }
}