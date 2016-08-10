<?php
namespace smpl\mydi;

/**
 * Interface LoaderInterface
 *
 * Отложенная загрузка, которая вызывается каждый раз, как запросят элемент в LocatorInterface
 * Которая должна вернуть результат, который вернет LocatorInterface
 * @package smpl\mydi
 */
interface LoaderInterface
{
    /**
     * Получает значение
     * @param LocatorInterface $locator
     * @return mixed
     */
    public function get(LocatorInterface $locator);
}