<?php
namespace smpl\mydi\loader\executor;

class Service extends AbstractExecutor
{

    public function execute($containerName, $config)
    {
        return new \smpl\mydi\container\Service($this->getClosure($containerName, $config));
    }
}