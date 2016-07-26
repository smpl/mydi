<?php
namespace Smpl\Mydi\Container;

use Smpl\Mydi\LocatorInterface;

/**
 * Class Service
 * @package Smpl\Mydi\Container
 * @see https://github.com/smpl/mydi/issues/2
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