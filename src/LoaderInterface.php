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
     * Проверяет может ли быть загружен контейнер
     * @param string $containerName
     * @return bool
     */
    public function has($containerName);

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить или некорректное имя
     * @return mixed
     */
    public function get($containerName);

    /**
     * Возвращает имена всех контейнеров что могут быть загружены.
     * @return array
    */
    public function getContainerNames();
}