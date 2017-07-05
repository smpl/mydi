<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;

trait ObjectTrait
{

    /**
     * @var \ReflectionClass
     */
    private $class;
    private $constructArgumentNames;

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
    private function setConstructArgumentNames(array $constructArgumentNames)
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
     */
    public static function factory(string $className, array $constructArgumentNames = [])
    {
        return new static(new \ReflectionClass($className), $constructArgumentNames);
    }

    public function get(ContainerInterface $locator)
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