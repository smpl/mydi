<?php
namespace smpl\mydi\loader;

interface DependencyExecutorInterface
{
    /**
     * @param string $containerName
     * @param array|string $config
     * @return mixed
     */
    public function execute($containerName, $config);
}