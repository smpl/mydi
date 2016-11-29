<?php
namespace smpl\mydi;

use Interop\Container\ContainerInterface;

/**
 * Interface LocatorInterface
 *
 * Расширенный ContainerInterface который позволяет
 *  * Использовать ArrayAccess
 *  * Смотреть карту зависимостей
 *  * Использовать другие ContainerInterface для разрешения зависимостей.
 * @package smpl\mydi
 */
interface LocatorInterface extends ContainerInterface
{

    /**
     * Добавить новое значение в контейнер
     * @param string $name Имя контейнера
     * @param mixed $value Здесь может быть любое значение.
     * @throws \InvalidArgumentException
     */
    public function set($name, $value);

    /**
     * Удалить значение по имени
     * @param $name
     * @throws \InvalidArgumentException
     */
    public function delete($name);

    /**
     * Возвращает карту зависимостей
     * @return array
     */
    public function getDependencyMap();
}