<?php
namespace smpl\mydi\loader;

use smpl\mydi\LocatorInterface;

/**
 * Class Service
 */
class Service extends Factory
{
    private $result;
    private $isCalled = false;

    public function get(LocatorInterface $locator)
    {
        if (!$this->isCalled) {
            $this->result = parent::get($locator);
            $this->isCalled = true;
        }
        return $this->result;
    }
}