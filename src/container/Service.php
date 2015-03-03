<?php
namespace smpl\mydi\container;

use smpl\mydi\LocatorInterface;

/**
 * Class Service
 * @package smpl\mydi\container
 * @see https://github.com/smpl/mydi/issues/2
 */
class Service extends Factory
{
    private $result;
    private $isCalled = false;

    public function resolve(LocatorInterface $locator)
    {
        if (!$this->isCalled) {
            $this->result = parent::resolve($locator);
            $this->isCalled = true;
        }
        return $this->result;
    }
}