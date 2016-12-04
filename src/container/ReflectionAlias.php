<?php
namespace smpl\mydi\container;

use smpl\mydi\ContainerException;
use smpl\mydi\loader\Alias;
use smpl\mydi\NotFoundException;

class ReflectionAlias extends AbstractReflection
{
    public function __construct($annotation = 'alias')
    {
        $this->setAnnotation($annotation);
    }

    public function get($id)
    {
        $target = null;
        $match = [];
        if (is_null($class = self::getReflection($id)) || !$this->has($id)) {
            throw new NotFoundException();
        }
        preg_match('#@' . $this->annotation . ' ([\w\\\\]*)#', $class->getDocComment(), $match);
        if (array_key_exists(1, $match)) {
            $target = $match[1];
        }
        if (is_null($target)) {
            throw new ContainerException('Alias target is unknow');
        }
        return new Alias($target);
    }

    public function has($id)
    {
        $result = false;
        $class = static::getReflection($id);
        if (!is_null($class) && strpos($class->getDocComment(), '@' . $this->annotation) !== false) {
            $result = true;
        }
        return $result;
    }

}