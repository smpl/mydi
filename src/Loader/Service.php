<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;

class Service extends Factory
{
    private $result;
    private $isCalled = false;

    public function load(ContainerInterface $container)
    {
        if (!$this->isCalled) {
            $this->result = parent::load($container);
            $this->isCalled = true;
        }
        return $this->result;
    }
}
