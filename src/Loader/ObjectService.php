<?php
namespace Smpl\Mydi\Loader;

use Interop\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

final class ObjectService implements LoaderInterface
{
    use ObjectTrait {
        get as getFactory;
    }

    private $isLoaded = false;
    private $result;

    public function get(ContainerInterface $locator)
    {
        if ($this->isLoaded === false) {
            $this->result = $this->getFactory($locator);
            $this->isLoaded = true;
        }
        return $this->result;
    }
}