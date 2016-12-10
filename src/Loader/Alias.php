<?php
namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

final class Alias implements LoaderInterface
{
    private $name;

    /**
     * Alias constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    private function setName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Name must be string');
        }
        $this->name = $name;
    }

    public function get(ContainerInterface $locator)
    {
        return $locator->get($this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}