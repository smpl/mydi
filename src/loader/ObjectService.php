<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

class ObjectService implements LoaderInterface
{
    use ObjectTrait {
        get as getFactory;
    }

    private $isLoaded = false;
    private $result;

    public function get(LocatorInterface $locator)
    {
        if ($this->isLoaded === false) {
            $this->result = $this->getFactory($locator);
            $this->isLoaded = true;
        }
        return $this->result;
    }
}