<?php
namespace Smpl\Mydi\Loader;

use Smpl\Mydi\LoaderInterface;

/**
 * Загрузка зависимостей на основе php файлов,
 * в случае если в имени контенейра указано _ то он трансформируется в DIRECTORY_SEPARATOR
 *
 * Class File
 * @package Smpl\Mydi\Loader
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

    public function __construct($basePath, $context = [])
    {
        $this->basePath = realpath($basePath);
        $this->setContext($context);
    }

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @throws \RuntimeException если у файла что подгружаем будет выводиться какой то текст
     * @return mixed
     */
    public function load($containerName)
    {
        if (!$this->isLoadable($containerName)) {
            throw new \InvalidArgumentException(sprintf('Container:`%s` must be loadable', $containerName));
        }
        ob_start();
        $output = $this->getContext();
        extract($output);
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

    public function getLoadableContainerNames()
    {
        $result = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->basePath,
                \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST);
        $iterator->rewind();
        while ($iterator->valid()) {
            /** @var \RecursiveDirectoryIterator $iterator */
            if ($iterator->isFile() && 'php' === $iterator->getExtension()) {
                $path = pathinfo($iterator->getSubPathName());
                if ($iterator->getSubPath() === '') {
                    $file = $path['filename'];
                } else {
                    $file = $path['dirname'] . DIRECTORY_SEPARATOR . $path['filename'];
                }
                $result[] = $this->pathToContainerName($file);
            }
            $iterator->next();
        }
        sort($result);
        return $result;
    }
    private function pathToContainerName($path)
    {
        $result = str_replace(DIRECTORY_SEPARATOR, '_', $path);
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

    /**
     * @return array
     */
    private function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    private function setContext(array $context)
    {
        $this->context = $context;
    }
}