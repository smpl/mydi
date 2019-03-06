<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\ProviderInterface;

class DynamicFile implements ProviderInterface
{
    /**
     * @var string
     */
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = (string)realpath($basePath);
    }

    public function provide(string $containerName)
    {
        if (!$this->hasProvide($containerName)) {
            throw new NotFound($containerName);
        }
        /** @psalm-suppress UnresolvableInclude */
        $result = require $this->containerNameToPath($containerName);
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
        $result = str_replace('\\', DIRECTORY_SEPARATOR, $containerName);
        $result = (string)realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
        return $result;
    }
}
