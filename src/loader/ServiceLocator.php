<?php
namespace smpl\mydi\loader;

use smpl\mydi\container\Factory;
use smpl\mydi\ContainerInterface;
use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

class ServiceLocator implements LoaderInterface
{

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @return Factory
     */
    public function load($containerName)
    {
        if (!$this->isLoadable($containerName)) {
            throw new \InvalidArgumentException(sprintf('Container:`%s`, must be loadable', $containerName));
        }
        $callback = function (LocatorInterface $locator) use ($containerName) {
            $result = call_user_func_array([$containerName, 'mydiLoad'], [$locator]);
            if ($result instanceof ContainerInterface) {
                $result = $result->resolve($locator);
            }
            return $result;
        };
        return new Factory($callback);
    }

    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function isLoadable($containerName)
    {
        if (!is_string($containerName)) {
            throw new \InvalidArgumentException('Container name must be a string');
        }
        if (array_key_exists(LocatorAwareInterface::class, class_implements($containerName))) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
}