<?php
namespace smpl\mydi;

/**
 * Предназначен для определения зависимостей на лету (в момент когда их запросят через LocatorInterface->resolve)
 *
 * Interface LoaderInterface
 * @package smpl\mydi
 */
interface LoaderInterface
{
    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function isLoadable($containerName);

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @return mixed
     */
    public function load($containerName);
}