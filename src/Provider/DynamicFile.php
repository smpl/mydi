<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundException;
use Smpl\Mydi\ProviderInterface;

final class DynamicFile implements ProviderInterface
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $basePath директория где хранятся файлы конфигурации
     */
    public function __construct($basePath)
    {
        $this->basePath = realpath($basePath);
    }

    public function get($containerName)
    {
        if (!$this->has($containerName)) {
            throw new NotFoundException;
        }
        /** @noinspection PhpIncludeInspection */
        $result = include $this->containerNameToPath($containerName);
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