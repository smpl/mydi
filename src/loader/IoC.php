<?php
namespace smpl\mydi\loader;

use smpl\mydi\loader\parser\Php;
use smpl\mydi\LoaderInterface;

/**
 * Загрузка зависимостей на основе php файлов,
 * в случае если в имени контенейра указано _ то он трансформируется в DIRECTORY_SEPARATOR
 *
 * Class File
 * @package smpl\mydi\loader
 */
class IoC implements LoaderInterface
{
    private $context;

    private $basePath;
    /**
     * @var Php[]
     */
    private $parsers = [];

    public function __construct($basePath, array $context = [])
    {
        $this->basePath = realpath($basePath);
        $this->setContext($context);
    }

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @throws \LogicException если у файла что подгружаем будет выводиться какой то текст
     * @return mixed
     */
    public function load($containerName)
    {
        if (!$this->isLoadable($containerName)) {
            throw new \InvalidArgumentException(sprintf('Container:`%s` must be loadable', $containerName));
        }
        $parser = $this->getParser($containerName);
        $result = $parser->parse();
        return $result;
    }

    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function isLoadable($containerName)
    {
        if (!is_string($containerName)) {
            throw new \InvalidArgumentException('Container name must be string');
        }
        $path = $this->containerNameToPath($containerName);
        if (substr($path, 0, strlen($this->basePath)) === $this->basePath) {
            $result = is_readable($this->containerNameToPath($containerName));
        } else {
            $result = false;    // Пытаются загрузить что то за пределами указанной папки
        }
        return $result;
    }

    /**
     * Можно переопредилить для того чтобы использовать свою структуру поиска файлов в зависимости от имени контейнера
     * @param string $containerName
     * @return string
     */
    protected function containerNameToPath($containerName)
    {
        $result = str_replace('_', DIRECTORY_SEPARATOR, $containerName);
        return realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
    }

    private function getParser($containerName)
    {
        if (!array_key_exists($containerName, $this->parsers)) {
            $this->parsers[$containerName] = new Php($this->containerNameToPath($containerName));
        }
        $this->parsers[$containerName]->setContext($this->getContext());
        return $this->parsers[$containerName];
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }
}