<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;
use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\Loader\Factory;
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
            $args = $this->getArgs($constructor);
            $args = array_merge($args, $this->readAnnotation((string)$constructor->getDocComment()));
        }

        return $this->createLoader((string)$class->getDocComment(), $class, $args);
    }

    private function getArgs(\ReflectionMethod $method)
    {
        $result = [];
        foreach ($method->getParameters() as $parameter) {
            $result[$parameter->name] = !is_null($parameter->getClass()) ? $parameter->getClass()->name : $parameter->name;
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

    private function createLoader(string $comment, \ReflectionClass $class, array $args)
    {
        if (strstr($comment, '@factory')) {
            $result = Factory::fromReflectionClass($class, $args);
        } else if (preg_match("/@alias ([\\\\\\w]*)/", $comment, $matches) && is_array($matches)) {
            $result = new Alias($matches[1]);
        } else {
            $result = Service::fromReflectionClass($class, $args);
        }
        return $result;
    }
}
