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
     * @var ParserInterface
     */
    private $parser;

    public function __construct(ParserInterface $parser)
    {
        $this->setParser($parser);
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

    private function getConfiguration()
    {
        if ($this->isLoad === false) {
            $this->setMap($this->parser->parse());
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
                sprintf(
                    'Config: `%s` must return array of configuration',
                    $this->getParser()->getFileName()
                )
            );
        }
        $this->map = $map;
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