<?php
namespace smpl\mydi\container;

use smpl\mydi\ContainerException;
use smpl\mydi\loader\Alias;
use smpl\mydi\NotFoundException;

class ReflectionAlias
{
    use ReflectionTrait;

    public function __construct($annotation = 'alias')
    {
        $this->setAnnotation($annotation);
    }

    public function get($id)
    {
        $class = static::getReflection($id);
        if (!$this->has($id)) {
            throw new NotFoundException();
        }
        return new Alias($this->getTarget($class));
    }

    private function getTarget(\ReflectionClass $class)
    {
        $match = [];
        preg_match('#@' . $this->annotation . ' ([\w\\\\]*)#', $class->getDocComment(), $match);
        if (!array_key_exists(1, $match) || is_null($match[1])) {
            throw new ContainerException('Alias target is unknow');
        }
        return $match[1];
    }


}