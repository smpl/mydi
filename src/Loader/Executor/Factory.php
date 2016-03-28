<?php
namespace Smpl\Mydi\Loader\Executor;

class Factory extends AbstractExecutor
{
    public function execute($containerName, $config)
    {
        return new \Smpl\Mydi\Container\Factory($this->getClosure($containerName, $config));
    }

}