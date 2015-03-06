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
        $this->setParser($parser);
    }

    /**
     * @return ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param ParserInterface $parser
     */
    public function setParser(ParserInterface $parser)
    {
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

    /**
     * Это вызывается в случае когда у Locator запросили построение дерева зависимостей,
     * Метод нужен исключительно разработчикам для анализа зависимостей и может не очень быстро работать
     * на production в обычной ситуации данный метод не должен вызываться
     * @return array
     */
    public function getAllLoadableName()
    {
        return array_keys($this->getConfiguration());
    }
}