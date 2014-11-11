<?php
namespace smpl\mydi;

/**
 * Interface LocatorInterface
 * Отвечает за добавление, хранение и разрешение зависимостей.
 * Каждой зависимости при добавление присвайвается уникальное имя, а для её разрешения его необходимо указать
 * @package smpl\mydi
 */
interface LocatorInterface {
    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function resolve($name);

    /**
     * @param string $name
     * @param $value
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function add($name, $value);
} 