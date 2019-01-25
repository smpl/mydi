<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Closure;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\ProviderInterface;

class DynamicFile implements ProviderInterface
{
    private $basePath;
    private $transform = false;

    public function __construct(string $basePath)
    {
        $this->basePath = (string)realpath($basePath);
    }

    public function transformClosureToService(): self
    {
        $this->transform = true;
        return $this;
    }

    public function provide(string $containerName)
    {
        if (!$this->hasProvide($containerName)) {
            throw new NotFound($containerName);
        }
        $result = require $this->containerNameToPath($containerName);
        return $this->transform($result);
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
        $result = str_replace('\\', DIRECTORY_SEPARATOR, $containerName);
        $result = realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
        $result = is_string($result) ? $result : '';
        return $result;
    }

    private function transform($result)
    {
        if ($this->transform === true && $result instanceof Closure) {
            $result = new Service($result);
        }
        return $result;
    }
}
