<?php

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;
use Smpl\Mydi\Loader\ObjectService;
use Smpl\Mydi\ProviderInterface;

class Autowiring implements ProviderInterface
{

    /**
     * @param string $name
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get(string $name)
    {
        try {
            $reflection = new \ReflectionClass($name);
            $comment = $reflection->getConstructor()->getDocComment();
            preg_match_all("/@inject ([\\\\\\w]*) (\\$[\\w]*)/", $comment, $matches, PREG_SET_ORDER);
            $arguments = [];
            foreach ($reflection->getConstructor()->getParameters() as $parameter) {
                $arguments[] = $this->getArgument($parameter, $matches);
            }
            return new ObjectService($reflection, $arguments);
        } catch (\Throwable $exception) {
            throw new NotFoundException();
        }
    }

    private function getArgument(\ReflectionParameter $parameter, array $matches)
    {
        $result = $parameter->name;
        if (!is_null($parameter->getClass())) {
            $result = $parameter->getClass()->getName();
        }
        foreach ($matches as $match) {
            if ($match[2] === '$' . $parameter->name) {
                $result = $match[1];
                break;
            }
        }
        return $result;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        $result = false;
        if (class_exists($name)) {
            $result = true;
        }
        return $result;
    }
}
