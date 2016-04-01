<?php
namespace Smpl\Mydi\Loader;

interface ExecutorInterface
{
    /**
     * @param string $containerName
     * @param array|string $config
     * @return mixed
     */
    public function execute($containerName, $config);
}