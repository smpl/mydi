<?php
namespace smpl\mydi\container;

class Service extends Factory
{
    private $result;
    private $isCalled = false;

    public function resolve()
    {
        if (!$this->isCalled) {
            $this->isCalled = true;
            $this->result = parent::resolve();
        }
        return $this->result;
    }
}