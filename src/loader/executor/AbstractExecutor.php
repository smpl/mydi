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
        if (!is_array($config) && !is_string($config)) {
            throw new \InvalidArgumentException('Config must be string or array');
        }
        return $this->getCallback($containerName, $config);
    }

    /**
     * @param $containerName
     * @param $config
     * @return callable
     */
    private function getCallback($containerName, $config)
    {
        if (is_array($config)) {
            $callback = $this->callbackFromArray($containerName, $config);
        } else {
            $callback = $this->callbackFromString($config);
        }
        return $callback;
    }

    /**
     * @param $containerName
     * @param $config
     * @return callable
     */
    private function callbackFromArray($containerName, $config)
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

    /**
     * @param string $config
     * @return callable
     */
    private function callbackFromString($config)
    {
        $callback = function (LocatorInterface $locator) use ($config) {
            return $locator->resolve($config);
        };
        return $callback;
    }
}