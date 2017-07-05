<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;

trait ReflectionObjectTrait
{
    use ReflectionTrait;

    /**
     * @var string
     */
    private $construct = '';

    protected function getLoader(string $id, string $loaderName)
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }
        $class = static::getReflection($id);
        return new $loaderName($class, $this->getInstanceArgs($class));
    }

    private function getInstanceArgs(\ReflectionClass $class): array
    {
        $result = [];
        $annotations = self::getInstanceByAnnotation(self::getConstructorDoc($class), $this->construct);
        foreach (self::getConstructorParameters($class) as $parameter) {
            $result[] = self::getResult($annotations, $parameter);
        }
        return $result;
    }

    private static function getInstanceByAnnotation(string $constructorDoc, string $annotation): array
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

    private static function getConstructorDoc(\ReflectionClass $class): string
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
    private static function getConstructorParameters(\ReflectionClass $class): array
    {
        return !is_null($class->getConstructor()) ? $class->getConstructor()->getParameters() : [];
    }

    private static function getResult(array $annotations, \ReflectionParameter $parameter): string
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

    protected function setConstruct(string $construct)
    {
        $this->construct = $construct;
    }
}