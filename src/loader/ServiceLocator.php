<?php
namespace smpl\mydi\loader;

use smpl\mydi\container\Factory;
use smpl\mydi\ContainerInterface;
use smpl\mydi\LocatorAwareInterface;
use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

class ServiceLocator implements LoaderInterface
{

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
     * Это вызывается в случае когда у Locator запросили построение дерева зависимостей,
     * Метод нужен исключительно разработчикам для анализа зависимостей и может не очень быстро работать
     * на production в обычной ситуации данный метод не должен вызываться
     * @return array
     */
    public function getAllLoadableName()
    {
        /**
         * Реализовать данный метод на текущий метод не предоставляется возможым
         * @link https://github.com/smpl/mydi/issues/49
         */
        return [];
    }
}