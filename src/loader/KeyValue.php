<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;

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
     * @var string
     */
    private $fileName;
    /**
     * @var ParserInterface
     */
    private $parser;

    public function __construct($fileName, ParserInterface $parser)
    {
        $this->fileName = $fileName;
        $this->parser = $parser;
    }

    private function getConfiguration()
    {
        if ($this->isLoad === false) {
            $this->map = $this->parser->parse($this->fileName);
            $this->isLoad = true;
        }
        return $this->map;
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
}