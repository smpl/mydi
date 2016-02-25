<?php
namespace smpl\mydi\loader\executor;

use smpl\mydi\container\Service as ContainerService;
use smpl\mydi\ContainerInterface;
use smpl\mydi\LocatorInterface;

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
        return new \smpl\mydi\container\Lazy(function (LocatorInterface $locatorInterface) {
            return $this->loaded->resolve($locatorInterface);
        });
    }
}