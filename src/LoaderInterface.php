<?php
namespace smpl\mydi;

interface LoaderInterface 
{
    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @return bool
     */
    public function isLoadable($containerName);

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @return mixed
     */
    public function load($containerName);
}