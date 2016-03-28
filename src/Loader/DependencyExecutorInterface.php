<?php
namespace Smpl\Mydi\Loader;

interface DependencyExecutorInterface
{
    /**
     * @param string $containerName
     * @param array|string $config
     * @return mixed
     */
    public function execute($containerName, $config);
}