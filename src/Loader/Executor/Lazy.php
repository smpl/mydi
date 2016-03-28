<?php
namespace Smpl\Mydi\Loader\Executor;

use Smpl\Mydi\Container\Service as ContainerService;
use Smpl\Mydi\ContainerInterface;
use Smpl\Mydi\LocatorInterface;

class Lazy extends AbstractExecutor
{
    /**
     * @var ContainerInterface
     */
    private $loaded;
    private $isLoaded = false;

    public function execute($containerName, $config)
    {
        if (!$this->isLoaded) {
            $this->loaded = new ContainerService($this->getClosure($containerName, $config));
            $this->isLoaded = true;
        }
        return new \Smpl\Mydi\Container\Lazy(function (LocatorInterface $locatorInterface) {
            return $this->loaded->resolve($locatorInterface);
        });
    }
}