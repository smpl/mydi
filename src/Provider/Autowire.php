<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\Provider\Autowire\Reader\WithoutCache;
use Smpl\Mydi\Provider\Autowire\ReaderInterface;
use Smpl\Mydi\ProviderInterface;

class Autowire implements ProviderInterface
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public static function withoutCache(): self
    {
        return new Autowire(new WithoutCache());
    }

    public function provide(string $name)
    {
        $dependencies = $this->reader->getDependecies($name);
        $closure = function (ContainerInterface $container) use ($dependencies, $name): object {
            $arguments = [];
            foreach ($dependencies as $dependency) {
                $arguments[] = $container->get($dependency);
            }
            /** @psalm-suppress InvalidStringClass */
            return new $name(... $arguments);
        };

        return new Service($closure);
    }

    public function hasProvide(string $name): bool
    {
        return class_exists($name);
    }
}
