<?php
namespace smpl\mydi;

/**
 * Interface LocatorInterface
 * Отвечает за добавление, хранение и разрешение зависимостей.
 * Каждой зависимости при добавление присвайвается уникальное имя, а для её разрешения его необходимо указать
 * @package smpl\mydi
 */
interface LocatorInterface extends \ArrayAccess{
    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function resolve($name);

    /**
     * @param string $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function add($name, $value);

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