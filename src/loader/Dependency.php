<?php
namespace smpl\mydi\loader;

use smpl\mydi\loader\executor\Factory;
use smpl\mydi\loader\executor\Lazy;
use smpl\mydi\loader\executor\Service;

class Dependency extends KeyValue
{
    private static $executorsDefault;
    /**
     * @var string
     */
    private $defaultExecutorName;
    /**
     * @var DependencyExecutorInterface[]
     */
    private $executors = [];

    public function __construct(ParserInterface $parser, $defaultExecutorName = 'service', $executors = null)
    {
        if (is_null($executors)) {
            $executors = self::getDefaultExecutors();
        }
        $this->setDefaultExecutorName($defaultExecutorName);
        $this->setExecutors($executors);
        parent::__construct($parser);
    }

    /**
     * @return DependencyExecutorInterface[]
     */
    public static function getDefaultExecutors()
    {
        if (empty(self::$executorsDefault)) {
            $result['service'] = new Service();
            $result['factory'] = new Factory();
            $result['lazy'] = new Lazy();
            self::$executorsDefault = $result;
        }
        return self::$executorsDefault;
    }

    public function load($containerName)
    {
        $config = parent::load($containerName);
        if (is_array($config) && array_key_exists('executor', $config)) {
            $executorName = $config['executor'];
        } else {
            $executorName = $this->getDefaultExecutorName();
        }
        $result = $this->getExecutorByName($executorName)->execute($containerName, $config);
        return $result;
    }

    /**
     * @return string
     */
    public function getDefaultExecutorName()
    {
        return $this->defaultExecutorName;
    }

    /**
     * @param string $defaultExecutorName
     */
    public function setDefaultExecutorName($defaultExecutorName)
    {
        if (!is_string($defaultExecutorName)) {
            throw new \InvalidArgumentException('Default executor name must be string');
        }
        $this->defaultExecutorName = $defaultExecutorName;
    }

    /**
     * @param string $name
     * @return DependencyExecutorInterface
     */
    private function getExecutorByName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Container name must be a string');
        }
        if (!array_key_exists($name, $this->executors)) {
            throw new \InvalidArgumentException(sprintf('Executor: `%s`, not found', $name));
        }
        return $this->getExecutors()[$name];
    }

    /**
     * @return DependencyExecutorInterface[]
     */
    public function getExecutors()
    {
        return $this->executors;
    }

    /**
     * @param DependencyExecutorInterface[] $executors
     */
    public function setExecutors(array $executors)
    {
        foreach ($executors as $executorName => $executor) {
            if (!is_string($executorName)) {
                throw new \InvalidArgumentException('Executor name must be a string');
            }
            if (!($executor instanceof DependencyExecutorInterface)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Excecutor: `%s`, must implement DependencyExecutorInterface',
                        $executorName
                    )
                );
            }
        }
        $this->executors = $executors;
    }

}