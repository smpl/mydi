<?php
declare(strict_types=1);

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
    public function __construct(string $basePath)
    {
        $this->basePath = (string)realpath($basePath);
    }

    public function get(string $containerName)
    {
        if (!$this->has($containerName)) {
            throw new NotFoundException;
        }
        /** @noinspection PhpIncludeInspection */
        $result = include $this->containerNameToPath($containerName);
        return $result;
    }

    public function has(string $containerName): bool
    {
        $result = false;
        $path = $this->containerNameToPath($containerName);
        if (substr($path, 0, strlen($this->basePath)) === $this->basePath) {
            $result = is_readable($this->containerNameToPath($containerName));
        }

        return $result;
    }

    /**
     * Можно переопредилить для того чтобы использовать свою структуру поиска файлов в зависимости от имени контейнера
     * @param string $containerName
     * @return string
     */
    private function containerNameToPath(string $containerName): string
    {
        $result = str_replace('_', DIRECTORY_SEPARATOR, $containerName);
        $result = str_replace('\\', DIRECTORY_SEPARATOR, $result);
        $result = realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
        $result = is_string($result) ? $result : '';
        return $result;
    }
}
