<?php
namespace smpl\mydi\loader\executor;

class Lazy extends AbstractExecutor
{
    public function execute($containerName, $config)
    {
        return new \smpl\mydi\container\Lazy(parent::execute($containerName, $config));
    }

}