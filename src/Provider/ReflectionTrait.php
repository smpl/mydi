<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;

trait ReflectionTrait
{
    private static $reflections = [];

    /**
     * @var string
     */
    private $annotation = '';

    public function has(string $name): bool
    {
        $result = false;
        try {
            $class = static::getReflection($name);
            $docComment = $class->getDocComment() !== false ? $class->getDocComment() : '';
            if (strpos($docComment, '@' . $this->annotation) !== false
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
    protected static function getReflection(string $id): \ReflectionClass
    {
        if (!self::isReflection($id)) {
            throw new NotFoundException();
        }
        if (!array_key_exists($id, self::$reflections)) {
            self::$reflections[$id] = new \ReflectionClass($id);
        }
        return self::$reflections[$id];
    }

    private static function isReflection(string $id): bool
    {
        return class_exists($id) || interface_exists($id);
    }

    protected function setAnnotation(string $annotation)
    {
        $this->annotation = $annotation;
    }
}