<?php
namespace smpl\mydi\loader\executor;

class Factory extends AbstractExecutor
{
    public function execute($containerName, $config)
    {
        return new \smpl\mydi\container\Factory($this->getClosure($containerName, $config));
    }

}