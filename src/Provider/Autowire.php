<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\Loader\Factory;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\NotFoundException;
use Smpl\Mydi\Provider\Autowire\Reader;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    private $reader;

    public function __construct(Reader $reader = null)
    {
        if (null === $reader) {
            $reader = new Reader();
        }
        $this->reader = $reader;
    }

    public function get(string $name)
    {
        try {
            $class = new \ReflectionClass($name);
            if (($name = $this->reader->getAliasName((string)$class->getDocComment())) !== false) {
                $result = new Alias((string)$name);
            } else {
                $dependencies = [];
                if (null !== $constructor = $class->getConstructor()) {
                    $dependencies = $this->reader->getDependencies((string)$constructor->getDocComment(), ... $constructor->getParameters());
                }
                if ($this->reader->isFactory((string)$class->getDocComment())) {
                    $result = Factory::fromReflectionClass($class, $dependencies);
                } else {
                    $result = Service::fromReflectionClass($class, $dependencies);
                }
            }
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
}
