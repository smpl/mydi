<?php
namespace smpl\mydi\container;

use Interop\Container\ContainerInterface;

abstract class AbstractReflection implements ContainerInterface
{
    private static $reflections = [];
    /**
     * @var string
     */
    protected $annotation = '';
    /**
     * @var string
     */
    protected $construct = '';

    /**
     * @param string $id
     * @return \ReflectionClass|null
     */
    protected static function getReflection($id)
    {
        $result = null;
        if (array_key_exists($id, self::$reflections)) {
            $result = self::$reflections[$id];
        } else {
            if (static::isReflection($id)) {
                $result = new \ReflectionClass($id);
                self::$reflections[$id] = $result;
            }
        }
        return $result;
    }

    private static function isReflection($id)
    {
        return is_string($id) && (class_exists($id) || interface_exists($id));
    }

    protected function setAnnotation($annotation)
    {
        if (!is_string($annotation)) {
            throw new \InvalidArgumentException('Annotation must be string');
        }
        $this->annotation = $annotation;
    }

    protected function setConstruct($construct)
    {
        if (!is_string($construct)) {
            throw new \InvalidArgumentException('Annotation to constructor must be string');
        }
        $this->construct = $construct;
    }

    protected function getInstanceArgs(\ReflectionClass $class)
    {
        $result = [];
        $annotations = self::getInstanceByAnnotation(self::getConstructorDoc($class), $this->construct);
        foreach (self::getConstructorParameters($class) as $parameter) {
            $result[] = self::getResult($annotations, $parameter);
        }
        return $result;
    }

    private static function getInstanceByAnnotation($constructorDoc, $annotation)
    {
        $result = [];
        preg_match_all("/@$annotation ([\\\\\\w]*) (\\$[\\w]*)/", $constructorDoc, $matches, PREG_SET_ORDER);
        if (!is_null($matches) && is_array($matches)) {
            foreach ($matches as $match) {
                $result[$match[2]] = $match[1];
            }
        }
        return $result;
    }

    private static function getConstructorDoc(\ReflectionClass $class)
    {
        $result = '';
        if (!is_null($class->getConstructor()) && $class->getConstructor()->getDocComment() !== false) {
            $result = $class->getConstructor()->getDocComment();
        }
        return $result;
    }

    /**
     * @param \ReflectionClass $class
     * @return \ReflectionParameter[]
     */
    private static function getConstructorParameters(\ReflectionClass $class)
    {
        return !is_null($class->getConstructor()) ? $class->getConstructor()->getParameters() : [];
    }

    private static function getResult(array $annotations, \ReflectionParameter $parameter)
    {
        $name = '$' . $parameter->name;
        if (array_key_exists($name, $annotations)) {
            $result = $annotations[$name];
        } else {
            if (!empty($parameter->getClass()->name)) {
                $result = $parameter->getClass()->name;
            } else {
                $result = $parameter->name;
            }
        }
        return $result;
    }

}