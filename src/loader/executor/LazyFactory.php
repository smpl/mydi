<?php
namespace smpl\mydi\loader\executor;

use smpl\mydi\container\Lazy as LazyContainer;

class LazyFactory extends AbstractExecutor
{
    public function execute($containerName, $config)
    {
        return new LazyContainer(parent::execute($containerName, $config));
    }

}