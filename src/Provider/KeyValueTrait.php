<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\ContainerException;
use Smpl\Mydi\Exception\NotFoundException;

trait KeyValueTrait
{
    /**
     * @var bool
     */
    private $isLoad = false;
    /**
     * @var array
     */
    private $configuration = [];
    private $filePath;

    /**
     * @param string $filePath Полный путь до файла конфигурации
     */
    public function __construct($filePath)
    {
        if (!is_string($filePath)) {
            throw new \InvalidArgumentException('FilePath must be string');
        }
        $this->filePath = $filePath;
    }

    public function get($containerName)
    {
        if (!$this->has($containerName)) {
            throw new NotFoundException;
        }
        return $this->getConfiguration()[$containerName];
    }

    public function has($containerName)
    {
        return array_key_exists($containerName, $this->getConfiguration());
    }

    /**
     * @return array
     */
    private function getConfiguration()
    {
        if ($this->isLoad === false) {
            $this->configuration = $this->loadFile($this->filePath);
            $this->isLoad = true;
        }
        return is_array($this->configuration) ? $this->configuration : [];
    }

    /**
     * @param string $filePath полный путь до файла конфигурации
     * @return array Результат в виде ассоциативного массива
     * @throws ContainerException в случае если проблемы с загрузкой файла
     */
    abstract protected function loadFile($filePath);
}