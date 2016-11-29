<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

class ObjectFactory implements LoaderInterface
{
    protected $class;
    protected $constructArgumentNames;

    /**
     * @param \ReflectionClass $class
     * @param string[] $constructArgumentNames
     */
    public function __construct(\ReflectionClass $class, array $constructArgumentNames = [])
    {
        $this->class = $class;
        $this->constructArgumentNames = $constructArgumentNames;
    }

    /**
     * @param string $className Полное имя класса с namespace
     * @param string[] $constructArgumentNames Массив имен зависимостей конструктора
     * @return ObjectFactory
     * @throws \InvalidArgumentException Если className не строка
     */
    public static function factory($className, array $constructArgumentNames = [])
    {
        if (!is_string($className)) {
            throw new \InvalidArgumentException('ClassName must be string');
        }
        $class = new \ReflectionClass($className);
        return new static($class, $constructArgumentNames);
    }

    public function get(LocatorInterface $locator)
    {
        $arguments = [];
        if (!empty($this->constructArgumentNames)) {
            foreach ($this->constructArgumentNames as $name) {
                $arguments[] = $locator->get($name);
            }
        }
        return $this->class->newInstanceArgs($arguments);
    }
}