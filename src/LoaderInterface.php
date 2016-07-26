<?php
namespace Smpl\Mydi;

/**
 * Предназначен для определения зависимостей на лету (в момент когда их запросят через LocatorInterface->get)
 *
 * Interface LoaderInterface
 * @package Smpl\Mydi
 */
interface LoaderInterface
{
    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function has($containerName);

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @return mixed
     */
    public function get($containerName);

    /**
     * Это вызывается в случае когда у Locator запросили построение дерева зависимостей,
     * Метод нужен исключительно разработчикам для анализа зависимостей и может не очень быстро работать
     * @return array
    */
    public function getLoadableContainerNames();
}