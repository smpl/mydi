<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Closure;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\ProviderInterface;

class DynamicFile implements ProviderInterface
{
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var bool
     */
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
        /** @psalm-suppress UnresolvableInclude */
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
        $result = (string)realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
        return $result;
    }

    /**
     * @param mixed $result
     * @return mixed
     */
    private function transform($result)
    {
        if ($this->transform === true && $result instanceof Closure) {
            $result = new Service($result);
        }
        return $result;
    }
}
