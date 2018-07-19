<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;

class Service extends Factory
{
    private $result;
    private $isCalled = false;

    public function get(ContainerInterface $locator)
    {
        if (!$this->isCalled) {
            $this->result = parent::get($locator);
            $this->isCalled = true;
        }
        return $this->result;
    }
}
