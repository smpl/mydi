<?php
namespace Smpl\Mydi\Loader\Executor;

class Service extends AbstractExecutor
{

    public function execute($containerName, $config)
    {
        return new \Smpl\Mydi\Container\Service($this->getClosure($containerName, $config));
    }
}