<?php
namespace Smpl\Mydi\Loader;

use Smpl\Mydi\LoaderInterface;

abstract class AbstractKeyValue implements LoaderInterface
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

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @return mixed
     */
    public function load($containerName)
    {
        if (!$this->isLoadable($containerName)) {
            throw new \InvalidArgumentException(sprintf('Container:`%s`, must be loadable', $containerName));
        }
        return $this->getConfiguration()[$containerName];
    }

    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function isLoadable($containerName)
    {
        return array_key_exists($containerName, $this->getConfiguration());
    }

    public function getLoadableContainerNames()
    {
        return array_keys($this->getConfiguration());
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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    abstract protected function loadFile($fileName);
}