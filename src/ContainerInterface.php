<?php
namespace smpl\mydi;

interface ContainerInterface
{
    /**
     * Данный метод вызывается у контейнера когда кто то запросил у LocatorInterface вызвал метод resolve
     * @return mixed значение которое храниться в данном контейнере
     */
    public function resolve();
} 