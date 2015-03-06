<?php
namespace smpl\mydi\loader\executor;

class Service extends AbstractExecutor
{

    public function execute($containerName, $config)
    {
        return new \smpl\mydi\container\Service(parent::execute($containerName, $config));
    }
}