<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Closure;
use Smpl\Mydi\Exception\NotFound;
use Smpl\Mydi\Loader\Service;
use Smpl\Mydi\ProviderInterface;

class KeyValue implements ProviderInterface
{
    private $configuration;
    private $transform = false;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function transformClosureToService(): self
    {
        $this->transform = true;
        return $this;
    }

    public static function fromJson(string $fileName): self
    {
        if (!is_readable($fileName)) {
            $message = sprintf('fileName: `%s` is not readable', $fileName);
            throw new \RuntimeException($message);
        }
        $configuration = json_decode((string)file_get_contents($fileName), true);
        if (!is_array($configuration)) {
            $message = sprintf('fileName: `%s` return invalid result', $fileName);
            throw new \RuntimeException($message);
        }
        return new self($configuration);
    }

    public static function fromPhp(string $fileName): self
    {
        if (!is_readable($fileName)) {
            $message = sprintf('fileName: `%s` is not readable', $fileName);
            throw new \RuntimeException($message);
        }
        $configuration = require $fileName;
        if (!is_array($configuration)) {
            $message = sprintf('fileName: `%s` return invalid result', $fileName);
            throw new \RuntimeException($message);
        }
        return new self($configuration);
    }

    public function provide(string $name)
    {
        if (!$this->hasProvide($name)) {
            throw new NotFound($name);
        }
        $result = $this->configuration[$name];
        return $this->transform($result);
    }


    public function hasProvide(string $name): bool
    {
        return array_key_exists($name, $this->configuration);
    }

    private function transform($result): self
    {
        if ($this->transform === true && $result instanceof Closure) {
            $result = new Service($result);
        }
        return $result;
    }
}
