<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\NotFoundException;
use Smpl\Mydi\ProviderInterface;

class KeyValue implements ProviderInterface
{
    private $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public static function fromJsonFile(string $fileName): self
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

    public static function fromPhpFile(string $fileName): self
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

    public function get(string $name)
    {
        if (!$this->has($name)) {
            throw new NotFoundException();
        }
        return $this->configuration[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->configuration);
    }
}
