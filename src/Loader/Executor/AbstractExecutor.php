<?php
namespace Smpl\Mydi\Loader\Executor;

use Smpl\Mydi\Loader\ExecutorInterface;
use Smpl\Mydi\LocatorInterface;

abstract class AbstractExecutor implements ExecutorInterface
{
    /**
     * @param $containerName
     * @param $config
     * @return \Closure
     */
    protected function getClosure($containerName, $config)
    {
        if (!is_array($config) && !is_string($config)) {
            throw new \InvalidArgumentException('Config must be string or array');
        }
        return $this->getCallback($containerName, $config);
    }

    /**
     * @param $containerName
     * @param $config
     * @return \Closure
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
     * @return \Closure
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
     * @return \Closure
     */
    private function callbackFromString($config)
    {
        $callback = function (LocatorInterface $locator) use ($config) {
            return $locator->resolve($config);
        };
        return $callback;
    }
}