<?php
namespace Smpl\Mydi\Loader;

use Smpl\Mydi\LoaderInterface;

class KeyValue implements LoaderInterface
{
    /**
     * @var bool
     */
    private $isLoad = false;
    /**
     * @var array
     */
    private $map = [];

    /**
     * @var Readerinterface
     */
    private $loader;

    public function __construct(Readerinterface $loader)
    {
        $this->loader = $loader;
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
            $this->setMap($this->loader->getConfiguration());
            $this->isLoad = true;
        }
        return $this->map;
    }

    /**
     * @param array $map
     */
    private function setMap($map)
    {
        if (!is_array($map)) {
            throw new \RuntimeException(
                'Loader Configuration must return array of configuration'
            );
        }
        $this->map = $map;
    }
}