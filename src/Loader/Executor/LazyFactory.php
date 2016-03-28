<?php
namespace Smpl\Mydi\Loader\Executor;

use Smpl\Mydi\Container\Lazy as LazyContainer;

class LazyFactory extends AbstractExecutor
{
    public function execute($containerName, $config)
    {
        return new LazyContainer($this->getClosure($containerName, $config));
    }

}