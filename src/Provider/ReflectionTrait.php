<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

trait ReflectionTrait
{
    private static $reflections = [];

    /**
     * @var string
     */
    private $annotation = '';

    public function has(string $name): bool
    {
        $docComment = $this->getDocComment($name);
        $result = $this->isValidDocComment($docComment);
        return $result;
    }

    /**
     * @param string $id
     * @return \ReflectionClass
     */
    protected static function getReflection(string $id): \ReflectionClass
    {
        if (!array_key_exists($id, self::$reflections)) {
            self::$reflections[$id] = new \ReflectionClass($id);
        }
        return self::$reflections[$id];
    }

    protected function setAnnotation(string $annotation)
    {
        $this->annotation = $annotation;
    }

    private function isValidDocComment(string $docComment): bool
    {
        $result = false;
        if (strpos($docComment, '@' . $this->annotation) !== false
            || empty($this->annotation)
        ) {
            $result = true;
        }
        return $result;
    }

    private function getDocComment(string $name): string
    {
        try {
            $class = static::getReflection($name);
            $docComment = $class->getDocComment() !== false ? $class->getDocComment() : '';
        } catch (\Exception $e) {
            $docComment = '';
        }
        return $docComment;
    }
}