<?php
namespace smpl\mydi;

interface LoaderInterface
{
    /**
     * Данный метод вызывается у контейнера когда кто то запросил у LocatorInterface вызвал метод get
     * @param LocatorInterface $locator
     * @return mixed значение которое храниться в данном контейнере
     */
    public function get(LocatorInterface $locator);
}