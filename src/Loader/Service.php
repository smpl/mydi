<?php
namespace Smpl\Mydi\Loader;

use Psr\Container\ContainerInterface;
use Smpl\Mydi\LoaderInterface;

final class Service implements LoaderInterface
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

    public function get(ContainerInterface $locator)
    {
        if (!$this->isCalled) {
            $this->result = call_user_func_array($this->callback, [$locator]);
            $this->isCalled = true;
        }
        return $this->result;
    }
}