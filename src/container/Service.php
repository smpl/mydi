<?php
namespace smpl\mydi\container;

/**
 * Class Service
 * @package smpl\mydi\container
 * @see https://github.com/smpl/mydi/issues/2
 */
class Service extends Factory
{
    private $result;
    private $isCalled = false;

    public function resolve()
    {
        if (!$this->isCalled) {
            $this->result = parent::resolve();
            $this->isCalled = true;
        }
        return $this->result;
    }
}