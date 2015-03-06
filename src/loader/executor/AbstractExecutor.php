<?php
namespace smpl\mydi\loader\executor;

use smpl\mydi\loader\DependencyExecutorInterface;
use smpl\mydi\LocatorInterface;

abstract class AbstractExecutor implements DependencyExecutorInterface
{

    /**
     * @param string $containerName
     * @param array $config
     * @return mixed
     */
    public function execute($containerName, $config)
    {
        $callback = function (LocatorInterface $locator) use ($containerName, $config) {
            $class = array_key_exists('class', $config) ? $config['class'] : $containerName;
            $constructInjection = array_key_exists('construct', $config) ? $config['construct'] : [];
            $args = [];
            foreach ($constructInjection as $container) {
                $args[] = $locator->resolve($container);
            }
            $reflect = new \ReflectionClass($class);
            $result = $reflect->newInstanceArgs($args);
            return $result;
        };
        return $callback;
    }
}