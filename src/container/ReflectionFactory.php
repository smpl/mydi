<?php
namespace smpl\mydi\container;

use smpl\mydi\loader\ObjectFactory;

class ReflectionFactory
{
    use ReflectionObjectTrait;

    /**
     * AbstractExecutor constructor.
     * @param string $annotation Имя анотация в заголовке класс, для которого будет применяться данный Executor
     * @param string $construct Имя анотации что может быть использованна в конструкторе для переопределения зависимостей
     */
    public function __construct($annotation = 'factory', $construct = 'inject')
    {
        $this->setAnnotation($annotation);
        $this->setConstruct($construct);
    }

    public function get($id)
    {
        return $this->getLoader($id, ObjectFactory::class);
    }
}