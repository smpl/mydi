<?php
namespace smpl\mydi;

use Interop\Container\ContainerInterface;

/**
 * Предназначен для определения зависимостей на лету (в момент когда их запросят через LocatorInterface->get)
 *
 * Interface LoaderInterface
 * @package smpl\mydi
 */
interface LoaderInterface extends ContainerInterface
{

    /**
     * Возвращает имена всех контейнеров что могут быть загружены.
     * @return array
    */
    public function getContainerNames();
}