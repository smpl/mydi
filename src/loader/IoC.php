<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;
use smpl\mydi\NotFoundException;

/**
 * Загрузка зависимостей на основе php файлов,
 * в случае если в имени контенейра указано _ то он трансформируется в DIRECTORY_SEPARATOR
 *
 * Class File
 * @package smpl\mydi\loader
 */
class IoC implements LoaderInterface
{
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var array
     */
    private $context;

    public function __construct($basePath, array $context = [])
    {
        $this->basePath = realpath($basePath);
        $this->context = $context;
    }

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @throws \RuntimeException если у файла что подгружаем будет выводиться какой то текст
     * @return mixed
     */
    public function get($containerName)
    {
        if (!$this->has($containerName)) {
            throw new NotFoundException(sprintf('Container: `%s`, is not defined', $containerName));
        }
        ob_start();
        extract($this->context);
        /** @noinspection PhpIncludeInspection */
        $result = include $this->containerNameToPath($containerName);
        $output = ob_get_clean();
        if (!empty($output)) {
            throw new \RuntimeException(sprintf(
                'File: `%s` must have empty output: `%s`',
                $this->containerNameToPath($containerName),
                $output
            ));
        }
        return $result;
    }

    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function has($containerName)
    {
        $result = false;
        if (is_string($containerName)) {
            $path = $this->containerNameToPath($containerName);
            if (substr($path, 0, strlen($this->basePath)) === $this->basePath) {
                $result = is_readable($this->containerNameToPath($containerName));
            }
        }
        return $result;
    }

    public function getContainerNames()
    {
        $result = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->basePath)
        );
        /** @var \SplFileInfo $value */
        foreach ($iterator as $value) {
            $tmp = $this->fileToContainerName($value);
            if (!is_null($tmp)) {
                $result[] = $tmp;
            }
        }
        sort($result);
        return $result;
    }

    private function fileToContainerName(\SplFileInfo $file)
    {
        $result = null;
        if ($file->isFile() && $file->getExtension() === 'php') {
            $pathInfo = pathinfo(substr($file->getRealPath(), strlen($this->basePath)));
            if ($pathInfo['dirname'] === '/') {
                $result = $pathInfo['filename'];
            } else {
                $result = str_replace(DIRECTORY_SEPARATOR, '_', substr($pathInfo['dirname'], 1));
                $result .= '_';
                $result .= $pathInfo['filename'];
            }
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
        $result = str_replace('\\', DIRECTORY_SEPARATOR, $result);
        return realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
    }
}