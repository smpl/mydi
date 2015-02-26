<?php
namespace smpl\mydi;

/**
 * Interface LocatorInterface
 * Отвечает за добавление, хранение и разрешение зависимостей.
 * Каждой зависимости при добавление присвайвается уникальное имя, а для её разрешения его необходимо указать
 * @package smpl\mydi
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
     * @param string $name Имя контейнера должно быть уникально
     * @param mixed $value Здесь может быть любое значение или даже объект с интерфейсом ContainerInterface
     * @throws \InvalidArgumentException
     */
    public function add($name, $value);

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
    public function isExist($name);

    /**
     * @return LoaderInterface[]
     */
    public function getLoaders();

    /**
     * @param LoaderInterface[] $loaders
     * @throw \InvalidArgumentException
     */
    public function setLoaders(array $loaders);

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @param string $name
     * @param $value
     */
    public function __set($name, $value);
}