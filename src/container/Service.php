<?php
namespace smpl\mydi\container;

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