<?php
namespace smpl\mydi\loader;

use smpl\mydi\LocatorInterface;

trait ObjectTrait
{

    /**
     * @var \ReflectionClass
     */
    protected $class;
    protected $constructArgumentNames;

    /**
     * @param \ReflectionClass $class
     * @param string[] $constructArgumentNames
     */
    public function __construct(\ReflectionClass $class, array $constructArgumentNames = [])
    {
        $this->class = $class;
        $this->setConstructArgumentNames($constructArgumentNames);
    }

    /**
     * @param string[] $constructArgumentNames
     */
    protected function setConstructArgumentNames($constructArgumentNames)
    {
        foreach ($constructArgumentNames as $argumentName) {
            if (!is_string($argumentName)) {
                throw new \InvalidArgumentException('Constructor arguments must be array of string');
            }
        }
        $this->constructArgumentNames = $constructArgumentNames;
    }

    /**
     * @param string $className Полное имя класса с namespace
     * @param string[] $constructArgumentNames Массив имен зависимостей конструктора
     * @return static
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