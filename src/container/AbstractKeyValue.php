<?php
namespace smpl\mydi\container;

use Interop\Container\ContainerInterface;
use smpl\mydi\ContainerException;
use smpl\mydi\NotFoundException;

abstract class AbstractKeyValue implements ContainerInterface
{
    /**
     * @var bool
     */
    private $isLoad = false;
    /**
     * @var array
     */
    private $configuration = [];
    private $fileName;

    public function __construct($fileName)
    {
        if (!is_string($fileName)) {
            throw new \InvalidArgumentException('FileName must be string');
        }
        $this->fileName = $fileName;
    }

    public function get($containerName)
    {
        if (!$this->has($containerName)) {
            throw new NotFoundException;
        }
        return $this->getConfiguration()[$containerName];
    }

    public function has($containerName)
    {
        return array_key_exists($containerName, $this->getConfiguration());
    }

    /**
     * @return array
     */
    private function getConfiguration()
    {
        if ($this->isLoad === false) {
            $this->configuration = $this->loadFile($this->fileName);
            $this->isLoad = true;
        }
        return is_array($this->configuration) ? $this->configuration : [];
    }

    /**
     * @param $fileName
     * @return array
     * @throws ContainerException
     */
    abstract protected function loadFile($fileName);
}