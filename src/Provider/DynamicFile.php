<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\NotFoundException;
use Smpl\Mydi\ProviderInterface;

class DynamicFile implements ProviderInterface
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = (string)realpath($basePath);
    }

    public function provide(string $containerName)
    {
        if (!$this->hasProvide($containerName)) {
            throw new NotFoundException;
        }
        /** @noinspection PhpIncludeInspection */
        $result = include $this->containerNameToPath($containerName);
        return $result;
    }

    public function hasProvide(string $containerName): bool
    {
        $result = false;
        $path = $this->containerNameToPath($containerName);
        if (substr($path, 0, strlen($this->basePath)) === $this->basePath) {
            $result = is_readable($this->containerNameToPath($containerName));
        }

        return $result;
    }

    private function containerNameToPath(string $containerName): string
    {
        $result = str_replace('_', DIRECTORY_SEPARATOR, $containerName);
        $result = str_replace('\\', DIRECTORY_SEPARATOR, $result);
        $result = realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
        $result = is_string($result) ? $result : '';
        return $result;
    }
}
