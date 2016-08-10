<?php
namespace smpl\mydi\container;

use Interop\Container\ContainerInterface;
use smpl\mydi\ContainerException;
use smpl\mydi\NotFoundException;

class IoC implements ContainerInterface
{
    /**
     * @var string
     */
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = realpath($basePath);
    }

    public function get($containerName)
    {
        if (!$this->has($containerName)) {
            throw new NotFoundException(sprintf('Container: `%s`, is not defined', $containerName));
        }
        ob_start();
        /** @noinspection PhpIncludeInspection */
        $result = include $this->containerNameToPath($containerName);
        $output = ob_get_clean();
        if (!empty($output)) {
            throw new ContainerException(sprintf(
                'File: `%s` must have empty output: `%s`',
                $this->containerNameToPath($containerName),
                $output
            ));
        }
        return $result;
    }

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