<?php
namespace Smpl\Mydi;

/**
 * Interface LocatorInterface
 * Отвечает за добавление, хранение и разрешение зависимостей.
 * Каждой зависимости при добавление присвайвается уникальное имя, а для её разрешения его необходимо указать
 * @package Smpl\Mydi
 */
interface LocatorInterface extends \ArrayAccess
{
    /**
     * Разрешить зависимость по её имени
     * @param string $name имя зависимости
     * @return mixed Значение которое хранилось в этом контейнере
     * @throws \InvalidArgumentException
     */
    public function resolve($name);

    /**
     * Добавить новый контейнер с именем $name и значение $value
     * @param string $name Имя контейнера
     * @param mixed $value Здесь может быть любое значение или даже объект с интерфейсом ContainerInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value);

    /**
     * @param $name
     * @throws \InvalidArgumentException
     */
    public function delete($name);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @return LoaderInterface[]
     */
    public function getLoaders();

    /**
     * @param LoaderInterface[] $loader
     * @throw \InvalidArgumentException
     */
    public function setLoaders(array $loader);
}