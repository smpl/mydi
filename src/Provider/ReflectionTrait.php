<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;

trait ReflectionTrait
{
    private static $reflections = [];

    /**
     * @var string
     */
    private $annotation = '';

    public function has($id)
    {
        $result = false;
        try {
            $class = static::getReflection($id);
            if (strpos($class->getDocComment(), '@' . $this->annotation) !== false
                || empty($this->annotation)
            ) {
                $result = true;
            }
        } catch (NotFoundException $e) {
            // Ошибку обрабатывать не стоит, понятно что ответ будет false
            $result = false;
        }
        return $result;
    }

    /**
     * @param string $id
     * @throws NotFoundException В случае если id не может быть объектом reflection
     * @return \ReflectionClass
     */
    protected static function getReflection($id)
    {
        if (!self::isReflection($id)) {
            throw new NotFoundException();
        }
        if (!array_key_exists($id, self::$reflections)) {
            self::$reflections[$id] = new \ReflectionClass($id);
        }
        return self::$reflections[$id];
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
}