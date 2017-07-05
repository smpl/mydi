<?php
declare(strict_types=1);

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
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    private function setName(string $name)
    {
        $this->name = $name;
    }

    public function get(ContainerInterface $locator)
    {
        return $locator->get($this->name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}