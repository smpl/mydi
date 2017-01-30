<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

/**
 * Class Service
 */
class Service implements LoaderInterface
{
    /**
     * @var callable
     */
    protected $callback;
    private $result;
    private $isCalled = false;

    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    public function get(LocatorInterface $locator)
    {
        if (!$this->isCalled) {
            $this->result = call_user_func_array($this->callback, [$locator]);
            $this->isCalled = true;
        }
        return $this->result;
    }
}