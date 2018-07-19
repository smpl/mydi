<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    public function get(string $name)
    {
        try {
            $class = new \ReflectionClass($name);
        } catch (\ReflectionException $e) {
            throw new NotFoundException();
        }
        $args = [];
        $constructor = $class->getConstructor();
        if (!is_null($constructor)) {
            $comment = $constructor->getDocComment() === false ? '' : $constructor->getDocComment();

            $args = $this->getArgs($constructor);
            $args = array_merge($args, $this->readAnnotation($comment));
        }

        return Service::fromReflectionClass($class, $args);
    }

    private function getArgs(\ReflectionMethod $method)
    {
        $result = [];
        foreach ($method->getParameters() as $parameter) {
            $result[$parameter->getName()] = !is_null($parameter->getClass()) ? $parameter->getClass()->getName() : $parameter->getName();
        }
        return $result;
    }

    private function readAnnotation(string $comment)
    {
        $result = [];
        $matches = [];
        preg_match_all("/@inject ([\\\\\\w]*) \\$([\\w]*)/", $comment, $matches, PREG_SET_ORDER);
        foreach ((array)$matches as $match) {
            $result[$match[2]] = $match[1];
        }
        return $result;
    }

    public function has(string $name): bool
    {
        $result = false;
        if (class_exists($name)) {
            $result = true;
        }
        return $result;
    }
}
