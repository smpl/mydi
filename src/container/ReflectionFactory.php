<?php
namespace smpl\mydi\container;

use smpl\mydi\loader\ObjectFactory;
use smpl\mydi\NotFoundException;

class ReflectionFactory extends AbstractReflection
{
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
        if (!$this->has($id)) {
            throw new NotFoundException();
        }
        $class = static::getReflection($id);
        return new ObjectFactory($class, $this->getInstanceArgs($class));
    }

    public function has($id)
    {
        $result = false;
        if (!is_null($class = static::getReflection($id))
            && (
                strpos($class->getDocComment(), '@' . $this->annotation) !== false
                || empty($this->annotation)
            )
        ) {
            $result = true;
        }
        return $result;
    }
}