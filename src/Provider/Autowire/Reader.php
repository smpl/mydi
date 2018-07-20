<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider\Autowire;

class Reader
{
    public function getAliasName(string $comment)
    {
        preg_match("/@alias \\\\?([\w\d\\\\]*)/", (string)$comment, $matches);
        return array_key_exists(1, $matches) ? $matches[1] : false;
    }

    public function getDependencies(string $comment, \ReflectionParameter ... $parameters): array
    {
        $result = [];
        foreach ($parameters as $parameter) {
            $result[$parameter->getName()] = null !== $parameter->getClass() ? $parameter->getClass()->getName() : $parameter->getName();
        }
        return array_merge($result, $this->getAnnotationParameters($comment));
    }

    private function getAnnotationParameters(string $comment)
    {
        $result = [];
        $matches = [];
        preg_match_all("/@inject \\\\?([\w\d\\\\]*) \\$([\w\d]*)/", $comment, $matches, PREG_SET_ORDER);
        foreach ((array)$matches as $match) {
            $result[$match[2]] = $match[1];
        }
        return $result;
    }

    public function isFactory(string $comment): bool
    {
        return (bool)strstr($comment, '@factory');
    }
}
