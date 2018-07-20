<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;
use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\Loader\Factory;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    public function get(string $name)
    {
        try {
            $class = new \ReflectionClass($name);
            $result = $this->getLoader($class);
        } catch (\ReflectionException $e) {
            throw new NotFoundException();
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

    private function getLoader(\ReflectionClass $class): LoaderInterface
    {
        if (preg_match("/@alias \\\\?([\w\d\\\\]*)/", (string)$class->getDocComment(), $matches)) {
            $result = new Alias($matches[1]);
        } else {
            $result = $this->createLoader($class);
        }
        return $result;
    }

    private function createLoader(\ReflectionClass $class): LoaderInterface
    {
        $args = [];
        $constructor = $class->getConstructor();
        if (!is_null($constructor)) {
            $args = array_merge($this->getArgs($constructor), $this->readAnnotation((string)$constructor->getDocComment()));
        }
        if (strstr((string)$class->getDocComment(), '@factory')) {
            $result = Factory::fromReflectionClass($class, $args);
        } else {
            $result = Service::fromReflectionClass($class, $args);
        }
        return $result;
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
        preg_match_all("/@inject \\\\?([\w\d\\\\]*) \\$([\w\d]*)/", $comment, $matches, PREG_SET_ORDER);
        foreach ((array)$matches as $match) {
            $result[$match[2]] = $match[1];
        }
        return $result;
    }
}
