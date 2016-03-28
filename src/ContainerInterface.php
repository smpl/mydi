<?php
namespace Smpl\Mydi;

interface ContainerInterface
{
    /**
     * Данный метод вызывается у контейнера когда кто то запросил у LocatorInterface вызвал метод resolve
     * @param LocatorInterface $locator
     * @return mixed значение которое храниться в данном контейнере
     */
    public function resolve(LocatorInterface $locator);
}