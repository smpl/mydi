<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;

class Manager implements LoaderInterface
{
    /**
     * @var \SplObjectStorage
     */
    private $storage;

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = [])
    {
        $this->storage = new \SplObjectStorage;
        $this->addFromArray($loaders);
    }

    /**
     * @param LoaderInterface[] $loaders
     */
    private function addFromArray(array $loaders)
    {
        foreach ($loaders as $loader) {
            $this->attach($loader);
        }
    }

    public function attach(LoaderInterface $loader)
    {
        $this->storage->attach($loader);
    }

    public function contains(LoaderInterface $loader)
    {
        return $this->storage->contains($loader);
    }

    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function isLoadable($containerName)
    {
        return !is_null($this->getLoader($containerName));
    }

    private function getLoader($containerName)
    {
        $result = null;
        /** @var LoaderInterface $loader */
        foreach ($this->storage as $loader) {
            if ($loader->isLoadable($containerName)) {
                $result = $loader;
                break;
            }
        }
        return $result;
    }

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @return mixed
     */
    public function load($containerName)
    {
        $loader = $this->getLoader($containerName);
        if (is_null($loader)) {
            throw new \InvalidArgumentException(sprintf('Container:`%s`, must be loadable', $containerName));
        }
        return $loader->load($containerName);
    }

    /**
     * Это вызывается в случае когда у Locator запросили построение дерева зависимостей,
     * Метод нужен исключительно разработчикам для анализа зависимостей и может не очень быстро работать
     * на production в обычной ситуации данный метод не должен вызываться
     * @return array
     */
    public function getAllLoadableName()
    {
        $result = [];
        /** @var LoaderInterface $loader */
        foreach ($this->storage as $loader) {
            $result = array_merge($result, $loader->getAllLoadableName());
        }
        return array_values(array_unique($result));
    }
}