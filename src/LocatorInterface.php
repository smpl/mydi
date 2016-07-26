<?php
namespace Smpl\Mydi;

/**
 * Interface LocatorInterface
 * Отвечает за добавление, хранение и разрешение зависимостей.
 * Каждой зависимости при добавление присвайвается уникальное имя, а для её разрешения его необходимо указать
 * @package Smpl\Mydi
 */
interface LocatorInterface extends \ArrayAccess, LoaderInterface
{

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
     * Возвращает описание зависимостей между контейнерами в виде массива
     *
     * Где ключ это имя контейнера что вызывали, а массив значений это то что потребовалось вызывать для него
     * @return array
     */
    public function getDependencyMap();

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